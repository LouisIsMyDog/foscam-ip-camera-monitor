</div> <!-- .container-fluid -->
<?php if(basename($_SERVER['PHP_SELF']) != 'login.php') { ?>
	<footer class="footer">
		<div class="container">
			<p class="text-muted">Copyright <?php echo date("Y",time()); ?>, Emre Bilgin </p>
		</div>
	</footer>
	<?php } ?>
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
		<script src="/CCTV/includes/assets/js/bootstrap.min.js"> </script>
		<script src="/CCTV/includes/assets/js/responsive_table.js"> </script>
		<script src="/CCTV/includes/assets/bootstrap-datepicker-1.6.1-dist/js/bootstrap-datepicker.min.js"> </script>
		<script src="/CCTV/includes/assets/js/call_functions.js"> </script>
	</body>
</html>
<?php 

if (isset($database)) { $database->close_connection(); } ?>
