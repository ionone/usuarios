<footer class="footer">
    <p>&copy; 2017 davidbermudez@jerez.es</p>
</footer>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../js/bootstrap.min.js"></script>

<?php // ruta para el servidor de pruebas 
if ($ENTORNO == 1)
    $ruta = "http://ranking.esy.es/v2";
elseif ($ENTORNO == 2)
    $ruta = "..";
?>
<script src="<?php echo $ruta?>/js/moment.min.js"></script>
<script src="<?php echo $ruta?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo $ruta?>/js/bootstrap-datetimepicker.es.js"></script>

<!-- Calendars -->
<script type="text/javascript">
     $('#divMiCalendario1').datetimepicker({
          format: 'YYYY-MM-DD'
      });
      //$('#divMiCalendario1').data("DateTimePicker").show();
</script>

<script type="text/javascript">
     $('#divMiCalendario2').datetimepicker({
          format: 'YYYY-MM-DD'
      });
      //$('#divMiCalendario2').data("DateTimePicker").show();
</script>