<div id="<?php echo $id; ?>">
	<?php if(!empty($albums)): ?>
	<?php if($showNav): ?><p class="gallery_nav"><?php echo $name; ?></p><?php endif; ?>
	<ul>
	<?php foreach($albums as $album): ?>
		<li><a href="<?php echo $this->controller->createUrl('photos',array('dir'=>$album['name'])); ?>"><img src="<?php echo $album['thumb']; ?>" alt="description" /><?php echo $album['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
	<div class="newRow"></div>
	<?php elseif(!empty($images)): ?>
	<?php if($showNav): ?>
		<p class="gallery_nav"><?php echo CHtml::link($name,
			$this->controller->createUrl('photos')); ?> -> <?php echo $details['name']; ?></p>
		<?php if(!empty($details['description'])): ?>
		<p class="gallery_description"><?php echo $details['description']; ?></p>
		<?php endif; ?>
	<?php endif; ?>
	<ul>
	<?php
	$i=1;
	foreach($images as $image): ?>
		<li<?php if($i%$imagesPerRow==1) echo ' class="newRow"'; ?>><a href="<?php echo $image['url']; ?>"><img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['alt']; ?>" /><?php echo $image['alt']; ?></a></li>
	<?php
		$i++;
	endforeach; ?>
	</ul>
	<div class="newRow"></div>
	<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

	<?php endif; ?>
</div>