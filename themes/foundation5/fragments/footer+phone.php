    <div class="footer row padded">
        <hr/>

        <div style="text-align: right">
            <p><small>Page rendered in {elapsed_time} seconds using {memory_usage}.</small></p>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.5/js/foundation.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.5/js/foundation/foundation.alert.js"></script>
    <?php foreach ($external_scripts as $script) : ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>

    <script>
        $(document).foundation();
    </script>
</body>
</html>