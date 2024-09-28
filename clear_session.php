<?php
session_start();
unset($_SESSION['invoice_id']);
echo "Session cleared.";
