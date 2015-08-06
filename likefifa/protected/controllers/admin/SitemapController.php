<?php
class SitemapController extends BackendController {
	
	const SITEMAP_NAME = 'sitemap.xml';

	protected function getSectorLinks() {
		$sectors = 
			Sector::model()->
				ordered()->
				withActiveDoctors()->
				findAll();
				
		$links = array($this->createAbsoluteUrl('/doctor'));
		foreach ($sectors as $sector) {
			$links[] = $this->createAbsoluteUrl('/search/guess', array('rewriteName' => $sector->rewrite_name));
		}
		
		return $links;
	}
	
	protected function getDoctorLinks() {
		$doctors = 
			Doctor::model()->
				ordered()->
				onlyActive()->
				findAll();
				
		$links = array();
		foreach ($doctors as $doctor) {
			$links[] = $this->createAbsoluteUrl('/search/guess', array('rewriteName' => $doctor->rewrite_name ?: $doctor->id));	
		}
		
		return $links;
	}
	
	protected function getArticleLinks() {
		$articles = 
			Article::model()->
				ordered()->
				onlyActive()->
				findAll();
				
		$links = array($this->createAbsoluteUrl('/article/index'));
		foreach ($articles as $article) {
			$links[] = $this->createAbsoluteUrl('/article/view', array('rewriteName' => $article->rewrite_name ?: $article->id));
		}
		
		return $links;
	}
	
	protected function getSitemapPath() {
		return Yii::app()->basePath.'/../'.self::SITEMAP_NAME;
	}
	
	public function actionIndex() {
		$sitemapPath = 
			$this->getSitemapPath();
		
		if (Yii::app()->request->isPostRequest) {
			$links = 
				array_merge(
					$this->getSectorLinks(),
					$this->getDoctorLinks(),
					$this->getArticleLinks()
				);
			
			$links = 
				array_unique($links);
				
			$xml = new XMLWriter;
			$xml->openMemory();
			$xml->setIndent(true);
			
			$xml->startDocument('1.0', 'utf8');
			$xml->startElement('urlset');
			$xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($links as $link) {
				$xml->startElement('url');
				$xml->writeElement('loc', $link);
				$xml->endElement();
			}
			$xml->endElement();
			$xml->endDocument();
			
			file_put_contents($sitemapPath, $xml->flush());
		}
		
		$fileExists = file_exists($sitemapPath);
		$mtime = null;
		
		if ($fileExists) {
			$mtime = date('d.m.Y H:i:s', filemtime($sitemapPath));
		}
			
		$this->render('index', compact(
			'fileExists',
			'mtime'
		));
	}
	
}