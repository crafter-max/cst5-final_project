<?php
// Shared page footer — included by every view file.
// Closes layout divs opened by header.php, renders footer, loads Bootstrap JS.
?>

</div><!-- /.container-fluid -->
</div><!-- /.page-content -->

<footer>
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small">
                &copy; <?= date('Y') ?>
                <strong>Paralith System</strong>
                &mdash; All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end small mt-2 mt-md-0">
                In collaboration with
                <strong>Kruger Electronics</strong>
                &amp;
                <strong>Griffin Computers</strong>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
