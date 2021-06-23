<?php
/********MENGAKHIRI SESSION LOGIN***********/
  session_start();
  session_destroy();
 ?>
<script language="javascript">
top.location.href = 'index.php';
</script>
