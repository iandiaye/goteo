<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <form method="post" action="/admin/licenses">

        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $this['license']->id; ?>" />
        <input type="hidden" name="order" value="<?php echo $this['license']->order; ?>" />

        <label for="license-group">Grupo:</label><br />
        <select id="license-group" name="group">
            <option value="">Ninguno</option>
            <?php foreach ($this['groups'] as $id=>$name) : ?>
            <option value="<?php echo $id; ?>"<?php if ($id == $this['license']->group) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>
<br />
        <label for="license-name">Nombre:</label><br />
        <input type="text" name="name" id="license-name" value="<?php echo $this['license']->name; ?>" />
<br />
        <label for="license-description">Texto tooltip:</label><br />
        <textarea name="description" id="license-description" cols="60" rows="10"><?php echo $this['license']->description; ?></textarea>
<br />
        <label for="license-url">Url:</label><br />
        <input type="text" name="url" id="license-url" value="<?php echo $this['license']->url; ?>" />
<br />
        <label for="license-icons">Tipos:</label><br />
        <select id="license-icons" name="icons[]" multiple size="6">
            <?php foreach ($this['icons'] as $icon) : ?>
            <option value="<?php echo $icon->id; ?>"<?php if (in_array($icon->id, $this['license']->icons)) echo ' selected="selected"'; ?>><?php echo $icon->name; ?></option>
            <?php endforeach; ?>
        </select>


        <input type="submit" name="save" value="Guardar" />

        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    </form>

</div>