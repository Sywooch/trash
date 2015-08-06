<script>var articlesDetail = true;</script>
<div class="content-wrap content-pad-bottom">
	<div class="det-back"><a href="<?php use likefifa\components\helpers\ListHelper;

		echo $article->section ? $article->section->getSectionUrl() : '/articles/'; ?>">вернуться ко всем статьям</a></div>
	<div class="articles-detail_masters">
		<h4>Мастера <?php echo count($article->services) === 1 ? $article->services[0]->genitive_name : ($article->section ? $article->section->genitive_name : null);?></h4>
		<?php $specialization = ($article->section && $article->section->id != 23) ? $article->section : null; $serviceIds = $article->services ? ListHelper::buildPropList('id', $article->services) : null;
		foreach ($masters as $master):?>
			<div class="item">
				<div class="articles-detail_masters__left">
					<?php if($master->isUploaded()){?>
						<a href="<?php echo $master->getProfileUrl(); ?>" class="articles-detail_masters__photo"><img width="64" src="<?php echo $master->avatar(); ?>" alt="<?php echo $master->getFullName(); ?>" /></a>
					<?php }else{ ?>
						<?php if($master->gender == LfMaster::GENDER_FEMALE){?>
							<div class="articles-detail_masters__noph"></div>
						<?php }else{ ?>
							<div class="articles-detail_masters__noph articles-detail_masters__male"></div>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="articles-detail_masters__body">
					<div class="articles-detail_masters__name"><a href="<?php echo $master->getProfileUrl(); ?>"><?php echo $master->getFullName(); ?></a></div>
					<div class="articles-detail_masters__rating"><span class="stars png in-bl"><span style="width: <?php echo $master->getRating() / 5 * 100; ?>%" class="png"></span></span><?php echo $master->getRating(); ?></div>
					<?php if ($master->getFullAddress()): ?>
						<div class="metro">
							<?php $address = $master->getShortAddress(); if ($master->undergroundStation): ?>
								<a href="<?php echo $this->createSearchUrl(null, null, null, array($master->undergroundStation)); ?>"><i class="icon-metro png metro-l_<?php echo $master->undergroundStation->undergroundLine->id; ?>"></i><?php echo $master->undergroundStation->name; ?></a><?php if($address) echo ',';?>
							<?php endif; ?>
							<?php echo $address; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
				<?php if ($master->prices||$master->salon): ?>
					<?php $prices = LfPrice::model()->getPrices($master, null, $article->section); ?>
					<?php if ($prices): 
						shuffle($prices); 
						if(count($prices) > 3) $prices = array_slice($prices, 0, 3);
					?>
						<table width="100%" class="tbl-price">
							<?php while ($price = array_shift($prices)) { ?>
								<tr>
									<td><span><?php echo $price->service->name; ?></span></td>
									<td class="td-cost">
										<?php if ($price->price) { ?>
											<span>
												<?php echo $price->getPriceFormatted() ?> <i>р.</i>
												<?php if(!empty($price->service->unit)): ?>
													<i>/ <?php echo $price->service->unit ?></i>
												<?php endif; ?>
											</span>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</table>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endforeach;?>
		<div class="item" style="padding-bottom:2px;"><a href="<?php $service = count($article->services) === 1 ? $article->services[0] : null; echo $this->createSearchUrl($article->section, $service);?>">Все мастера <?php echo $service ? $service->genitive_name : ($article->section ? $article->section->genitive_name : null);?></a></div>
	</div>
	<div class="articles-detail_social">
		<div class="item in-bl">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_EN/all.js#xfbml=1&appId=192869677440481";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<div class="fb-like" data-send="false" data-layout="box_count" data-width="450" data-show-faces="true"></div>
		</div>
		<div class="item in-bl">
			<div id="vk_like-vertical"></div>
			<script type="text/javascript">
				VK.Widgets.Like("vk_like-vertical", {type: "vertical"});
			</script>
		</div>
		<div class="item in-bl">
			<div id="ok_shareWidget"></div>
			<script>
			!function (d, id, did, st) {
			  var js = d.createElement("script");
			  js.src = "http://connect.ok.ru/connect.js";
			  js.onload = js.onreadystatechange = function () {
			  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
			    if (!this.executed) {
			      this.executed = true;
			      setTimeout(function () {
			        OK.CONNECT.insertShareWidget(id,did,st);
			      }, 0);
			    }
			  }};
			  d.documentElement.appendChild(js);
			}(document,"ok_shareWidget",location.href,"{width:75,height:65,st:'straight',sz:20,ck:1,vt:'1'}");
			</script>
		</div>
		<div class="item in-bl">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-count="vertical">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
	</div>
	<div class="articles-detail_head"><div class="det-line_sep" style="margin-top: 21px;"><h1><?php echo $article->name; ?></h1></div></div>
	<div class="articles-detail">
		<?php echo $article->text; ?>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
	<?php if ($article->section): ?><div class="article-link_other" style="margin-top:-16px;"><a href="<?php echo $article->section->getSectionUrl(); ?>">все статьи в категории «<?php echo $article->section->name; ?>» (<?php echo count($article->section->articles); ?>)</a></div>
	<?php else:?><div class="article-link_other" style="margin-top:-16px;"><a href="<?php echo  Yii::app()->createUrl('article/index', array('sectionRewriteName' => 'other')); ?>">все статьи в категории «другое» (<?php echo count(Article::model()->findAll('article_section_id IS NULL')); ?>)</a></div>
	<?php endif;?>
	<?php if ($works): ?>
	<div class="det-line_sep"><span><?php echo count($article->services) === 1 ? su::ucfirst($article->services[0]->name).'. ' : ($article->section ? su::ucfirst($article->section->name).'. ' : null); ?>Фото лучших работ мастеров</span></div>
	<div class="det-works" style="margin-bottom:30px;">
		<?php $i=0; foreach ($works as $work): $i++;?>
			<div class="item<?php if($i == 1){ ?> first<?php } ?>">
				<div class="det-works_wrap">
					<a class="det-works_img"
					   data-header-url="<?php echo $work->master->getProfileUrl(); ?>"
					   title="<?php $alt = $work->alt ? : $work->service->name; echo $alt; ?>"
					   href="<?php echo $work->preview('full'); ?>"
					   rel="prettyPhoto[gallery1]">
						<img width="183" alt="<?php echo $alt; ?>" src="<?php echo $work->preview('small'); ?>"/>
					</a>
					<?php $social_link = 'http://likefifa.ru' . $work->preview('full'); ?>
					<?php $this->widget(
						'likefifa\components\likefifa\widgets\WorkVkWidget',
						[
							'master' => $work->master,
							'work'   => $work,
						]
					) ?>
				</div>
			</div>
		<?php endforeach?>	
		<div class="clearfix"></div>
	</div>
	<?php endif; ?>
	<div class="det-line_sep"><span>Другие статьи</span></div>
	<div class="articles-others">
		<?php $i=0; foreach ($articles as $a): $i++;?>
			<div class="item<?php if ($i == 1) { ?> first<?php } ?>">
				<div class="articles-head"><a href="<?php echo $a->getDetailUrl(); ?>"><?php echo $a->name; ?></a></div>
				<div class="articles-txt"><?php echo $a->description; ?></div>
			</div>
		<?php endforeach; ?>
		<div class="clearfix"></div>
	</div>
	<div class="article-link_other"><a href="/articles/">все статьи</a></div>
</div>

<script type="text/javascript">
	$(function() {
		initCardLikes();
	});
</script>
