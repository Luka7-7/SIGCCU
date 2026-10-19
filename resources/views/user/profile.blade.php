<x-layout.main title="Mi perfil · SIGCCU">
   <div class="page-wrapper">
      <div class="page-header">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-auto">
                  <span style="background-image: url(/static/avatar.jpg)" class="avatar avatar-lg"></span>
               </div>
               <div class="col">
                  <h1 class="fw-bold m-0">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</h1>
                  <div class="my-2">Descripción</div>

                  <div class="list-inline list-inline-dots text-secondary">
                     <div class="list-inline-item">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/map -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                           aria-hidden="true" focusable="false" class="icon icon-inline">
                           <path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13"></path>
                           <path d="M9 4v13"></path>
                           <path d="M15 7v13"></path>
                        </svg>
                        {{ auth()->user()->nombre_carrera }}
                     </div>
                     <div class="list-inline-item">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/mail -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                           aria-hidden="true" focusable="false" class="icon icon-inline">
                           <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10">
                           </path>
                           <path d="M3 7l9 6l9 -6"></path>
                        </svg>
                        <a href="#" class="text-reset">{{ auth()->user()->correo_electronico }}</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- BEGIN PAGE BODY -->
      <main id="content" class="page-body">
         <div class="container-xl">
            <div class="row g-3">
               <div class="col">

               </div>
               <div class="col-lg-4">
                  <div class="row row-cards">
                     <div class="col-12">

                     </div>
                  </div>
               
                  </div>
               </div>
            </div>
         </div>
   </div>
   </main>
   <!-- END PAGE BODY -->

   <!-- BEGIN FOOTER -->
   <!--  BEGIN FOOTER  -->
   <footer class="footer footer-transparent d-print-none">

   </footer>
   <!--  END FOOTER  -->
   <!-- END FOOTER -->
   </div>
</x-layout.main>