<?php
// This helper script was used for debugging the database during development.
// It's intentionally disabled to avoid exposing data in production environments.
http_response_code(410); // Gone
exit('This endpoint is disabled.');
?>
