 <!-- Jquery Core Js -->
 <script src="{{asset('assets/admin/bundles/libscripts.bundle.js')}}"></script>

 <!-- Plugin Js -->
 <script src="{{asset('assets/admin/bundles/apexcharts.bundle.js')}}"></script>
 <script src="{{asset('assets/admin/bundles/dataTables.bundle.js')}}"></script>

 <!-- Jquery Page Js -->
 <script src="{{asset('assets/admin/js/template.js')}}"></script>
 <script src="{{asset('assets/admin/js/page/index.js')}}"></script>
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1Jr7axGGkwvHRnNfoOzoVRFV3yOPHJEU&amp;callback=myMap"></script>
 <script src="{{asset('assets/admin/bundles/dropify.bundle.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper-init.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper.min.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper.min.js')}}"></script>

 
 <script>
     $('#myDataTable')
         .addClass('nowrap')
         .dataTable({
             responsive: true,
             columnDefs: [{
                 targets: [-1, -3],
                 className: 'dt-body-right'
             }]
         });
 </script>