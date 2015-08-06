<?xml version='1.0'  encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="cycle.xsl" />
    
    <xsl:decimal-format decimal-separator = '.' grouping-separator = ' ' NaN = ' '/>

    <xsl:output method="html" encoding="utf-8"/>

    
   <xsl:template match="/">
		<xsl:apply-templates select="root"/>
   </xsl:template>
   
   
   
   
	<xsl:template match="root">
		<xsl:for-each select="dbInfo/ReviewList/Element[position() &gt; 3]">
            <li class="reviews_item">
                <div class="review_info">
					<span class="review_overall_rating">
						<xsl:value-of select="RatInWord" />
					</span>
                    <span class="review_author">
                        <xsl:value-of select="Name"/>
                    </span>
                    <span class="review_date">
                        <xsl:value-of select="FormatedDate" />
                    </span>
                </div>
                <div class="review_content">
                    <ul class="review_ratings">
                        <li class="review_ratings_item">
                            <div class="l-ib js-tooltip-tr" title="Насколько врач подробно ответил на все вопросы по курсу лечения или диагностики.">
                                <span class="review_ratings_label">
                                    Квалификация
                                </span>
                                <!-- rating -->
                                <xsl:variable name="rating">
                                    <xsl:value-of select="(RatQualification)"/>
                                </xsl:variable>

                                <div class="rating_stars js-rating-small" data-score="{$rating}">
                                </div>
                                <!-- rating end -->
                            </div>
                        </li>
                        <li class="review_ratings_item">
                            <div class="l-ib js-tooltip-tr" title="Насколько врач был внимателен и тактичен по отношению к пациенту.">
                                <span class="review_ratings_label">
                                    Внимание
                                </span>
                                <!-- rating -->
                                <xsl:variable name="rating">
                                    <xsl:value-of select="(RatAttention)"/>
                                </xsl:variable>

                                <div class="rating_stars js-rating-small" data-score="{$rating}">
                                </div>
                                <!-- rating end -->
                            </div>
                        </li>
                        <li class="review_ratings_item">
                            <div class="l-ib js-tooltip-tr" title="Насколько цена приема соответствует качеству обслуживания и полученным результатам.">
                                <span class="review_ratings_label">
                                    Цена-качество
                                </span>
                                <!-- rating -->
                                <xsl:variable name="rating">
                                    <xsl:value-of select="(RatRoom)"/>
                                </xsl:variable>

                                <div class="rating_stars js-rating-small" data-score="{$rating}">
                                </div>
                                <!-- rating end -->
                            </div>
                        </li>
                    </ul>
                    <p class="review_text">
                        <xsl:value-of select="Review"/>
                    </p>
                    <!--
                    <div class="review_ratings_useful">???
                        Отзыв полезен? <span class="js-review-useful-yes t-green">Да</span> 16 / <span class="js-review-useful-no t-red">Нет</span> 4
                    </div>
                    -->
                </div>
            </li>
		</xsl:for-each>
	</xsl:template>
	

</xsl:transform>
