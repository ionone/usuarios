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

<script languague="javascript">
        function mostrar($texto_a_mostrar) {
            alti = document.getElementById($texto_a_mostrar);
            div = document.getElementById('information');
            //div.innerHTML = $texto_a_mostrar + ('<div id="close"><a href="javascript:cerrar();">cerrar</a></div>');
            div.innerHTML = alti.getAttribute('alt') + ('<div id="close"><a href="javascript:cerrar();">cerrar</a></div>');
            div.style.display = '';
        }

        function cerrar() {
            div = document.getElementById('information');
            div.style.display = 'none';
        }
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-103675901-1', 'auto');
  ga('send', 'pageview');

</script>
