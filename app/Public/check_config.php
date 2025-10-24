<?php
echo "<h2>Configuration PHP actuelle</h2>";
echo "<ul>";
echo "<li>upload_max_filesize = " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size = " . ini_get('post_max_size') . "</li>";
echo "<li>max_file_uploads = " . ini_get('max_file_uploads') . "</li>";
echo "<li>memory_limit = " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time = " . ini_get('max_execution_time') . "</li>";
echo "</ul>";
?>