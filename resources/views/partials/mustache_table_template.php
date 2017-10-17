<script id="template" type="text/html">
    <tr>

        <?php if (isset($fields)):


            foreach ($fields as $field):?>
                <?php switch ($field):
                    case 'link': ?>
                        <td><a target="_blank" href="{{record.<?= $field ?>}}">visit</a></td>
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