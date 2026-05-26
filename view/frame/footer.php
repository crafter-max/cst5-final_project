<?php
// footer.php — Shared page footer.
// Required at the bottom of every view file.
// Closes the container/page-content divs opened in header.php,
// then outputs the site footer and Bootstrap JS.
?>

</div><!-- /.container-fluid -->
</div><!-- /.page-content -->

<!-- ── Site Footer ─────────────────────────────────────────── -->
<footer class="py-3 fixed-bottom" style="background-color: #12172a; color: #8a96aa;">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small">
                &copy; <?= date('Y') ?>
                <strong class="text-white">Paralith System</strong>
                &mdash; All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end small mt-2 mt-md-0">
                In collaboration with
                <strong class="text-white">Kruger Electronics</strong>
                &amp;
                <strong class="text-white">Griffin Computers</strong>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
