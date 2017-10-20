<script id="template" type="text/html">
    <tr>

        <?php if (isset($fields)):


            foreach ($fields as $field):?>
                <?php switch ($field):
                    case 'link': ?>

                        <td>{{{record.<?= $field ?>}}}</td>
                        <?php break;
                    case 'app_links': ?>
                        <td>{{{record.<?= $field ?>}}}</td>
                        <?php break;
                        default: ?>
                        <td>{{{record.<?= $field ?>}}}</td>
                        <?php break; ?>


                    <?php endswitch;



            endforeach;


        endif; ?>


    </tr>
</script>