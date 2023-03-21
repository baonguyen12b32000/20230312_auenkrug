<?php

/**
 * @package         Convert Forms
 * @version         4.0.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
extract($displayData);

if (empty($images))
{
	return;
}

use NRFramework\Functions;

$value = !empty($value) ? $value : $images[0];
$heightAtt = !empty($height) ? ' style="height:' . $height . ';"' : '';

?>
<div class="nr-images-selector <?php echo $class; ?>" style="max-width: <?php echo $width;?>;">
	<?php
	if ($required)
	{
		?><input type="hidden" required class="required" id="<?php echo $id; ?>"/><?php
	}
	
	foreach ($images as $key => $img)
	{
		$id = "nr-images-selector-" . md5(uniqid() . $img);

		$atts = $heightAtt;

		$isPro = false;
		$class = '';

		if ($pro_items)
		{
			foreach ($pro_items as $item)
			{
				if (!Functions::endsWith($img, $item))
				{
					continue;
				}

				$isPro = true;
				$class = 'is-pro';
				$atts .= ' data-pro-only="' . JText::_('NR_THIS_STYLE') . '"';
			}
		}
		
		$item_value = $key_type === 'filename' ? pathinfo($img, PATHINFO_FILENAME) : $img;

		$isChecked = $value == $item_value ? ' checked="checked"' : '';
		?>
		<div class="nr-images-selector-item image<?php echo $class ? ' ' . $class : ''; ?>"<?php echo $atts; ?>>
			<?php if ($isPro): ?>
				<div class="is-pro-overlay"><span><i class="icon-lock"></i><i class="icon-unlock"></i></div>
			<?php endif; ?>
			<input type="radio" id="<?php echo $id; ?>" value="<?php echo !$isPro ? $item_value : ''; ?>" name="<?php echo !$isPro ? $name : ''; ?>"<?php echo $isChecked; ?> />
			<label for="<?php echo $id; ?>"><img src="<?php echo JURI::root() . $img; ?>" alt="<?php echo $img; ?>" /></label>
		</div>
		<?php
	}
	?>
</div>