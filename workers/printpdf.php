<?php 
function generate_pdf($file)
{
	$descriptorspec = array(
        0 => array('pipe', 'r'), // stdin
        1 => array('pipe', 'w'), // stdout
        2 => array('pipe', 'w'), // stderr
    );
    $process = proc_open('/usr/bin/xvfb-run /usr/bin/wkhtmltopdf -O landscape -q - -', $descriptorspec, $pipes);
    // Send the HTML on stdin
    fwrite($pipes[0], $file);
    fclose($pipes[0]);
    // Read the outputs
    $pdf = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    // Close the process
    fclose($pipes[1]);
    $return_value = proc_close($process);
    // Output the results
    if ($errors) {
        throw new Exception('PDF generation failed: ' . $errors);
    } else {
        header('Content-Type: application/x-download');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Length: ' . strlen($pdf));
        header('Content-Disposition: inline; filename="tabulka.pdf";');
        echo $pdf;
    }
}
$_POST['user']=$_GET['user'];
ob_start();
include '../ajax/trainings.php';
$string = ob_get_clean();
generate_pdf($string);
?>