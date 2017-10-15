<script id="template" type="text/html">
    <tr>

        <?php if (isset($fields)): ?>

            <?php
            foreach ($fields as $field):?>
                <?php if('link' == $field): ?>
                    <td><a target="_blank" href="{{record.<?= $field ?>}}">visit</a></td>
                    <?php else: ?>
                    <td>{{record.<?= $field ?>}}</td>
                    <?php endif; ?>

            <?php endforeach;
            ?>

        <?php endif; ?>


    </tr>
</script>