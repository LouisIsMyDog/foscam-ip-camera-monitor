</div> <!-- .container-fluid -->
<?php if (basename($_SERVER['PHP_SELF']) != 'login.php') {?>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Copyright
            <?php echo date("Y", time()); ?>, EBDesigns.us </p>
    </div>
</footer>
<?php }?>
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="/CCTV/includes/assets/js/bootstrap.min.js"> </script>
<script src="/CCTV/includes/assets/bootstrap-datepicker-1.6.1-dist/js/bootstrap-datepicker.min.js"> </script>
<script src="/CCTV/includes/assets/js/call_functions.js?ver=<?php echo time(); ?>"> </script>
<!-- <script src="/CCTV/node_modules/lightbox2/dist/js/lightbox.min.js"></script> -->
<script src="/CCTV/node_modules/bower/bin/bower_components/ekko-lightbox/dist/ekko-lightbox.min.js"></script>
</body>

</html>
<?php

if (isset($database)) {$database->closeConnection();}?>