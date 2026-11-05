<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceWindow
{
    private const CACHE_KEY = 'sigccu:attendance-windows';

    private const PENDING_IP = '0.0.0.0';

    public static function pendingIp(): string
    {
        return self::PENDING_IP;
    }

    public static function expiresAt(int $activityId): ?Carbon
    {
        $expiresAt = Cache::store('file')->get(self::CACHE_KEY, [])[$activityId] ?? null;

        return $expiresAt === null ? null : Carbon::createFromTimestamp($expiresAt);
    }

    public static function open(int $activityId, Carbon $expiresAt): void
    {
        self::withWindows(function (array $windows) use ($activityId, $expiresAt): array {
            $windows[$activityId] = $expiresAt->timestamp;

            return $windows;
        });
    }

    public static function expireActivity(int $activityId): void
    {
        self::withWindows(function (array $windows) use ($activityId): array {
            $expiresAt = $windows[$activityId] ?? null;

            if ($expiresAt !== null && $expiresAt <= now()->timestamp) {
                DB::table('asistencias')
                    ->where('conferencia_taller_id', $activityId)
                    ->where('Activo', true)
                    ->update(['Activo' => false]);

                unset($windows[$activityId]);
            }

            return $windows;
        });
    }

    public static function expireExpired(): void
    {
        self::withWindows(function (array $windows): array {
            $now = now()->timestamp;
            $expiredActivityIds = [];

            foreach ($windows as $activityId => $expiresAt) {
                if ($expiresAt <= $now) {
                    $expiredActivityIds[] = (int) $activityId;
                    unset($windows[$activityId]);
                }
            }

            if ($expiredActivityIds !== []) {
                DB::table('asistencias')
                    ->whereIn('conferencia_taller_id', $expiredActivityIds)
                    ->where('Activo', true)
                    ->update(['Activo' => false]);
            }

            $activeActivityIds = array_map('intval', array_keys($windows));
            $asistenciasActivas = DB::table('asistencias')->where('Activo', true);

            if ($activeActivityIds !== []) {
                $asistenciasActivas->whereNotIn('conferencia_taller_id', $activeActivityIds);
            }

            $asistenciasActivas->update(['Activo' => false]);

            return $windows;
        });
    }

    private static function withWindows(callable $callback): void
    {
        Cache::store('file')
            ->lock(self::CACHE_KEY.':lock', 10)
            ->block(5, function () use ($callback): void {
                $windows = Cache::store('file')->get(self::CACHE_KEY, []);
                $updatedWindows = $callback($windows);

                Cache::store('file')->forever(self::CACHE_KEY, $updatedWindows);
            });
    }
}
