<?php
/**
 * Sitemap stylesheet.
 *
 * @package    RankMath
 * @subpackage RankMath\Sitemap
 */

use RankMath\Helper;
use RankMath\Sitemap\Router;

defined( 'ABSPATH' ) || exit;

// Echo so opening tag doesn't get confused for PHP.
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<xsl:stylesheet version="2.0"
	xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:kml="http://www.opengis.net/kml/2.2"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:atom="http://www.w3.org/2005/Atom">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<xsl:choose>
					<xsl:when test="kml:kml">
						<title><?php echo esc_html( $kml_title ); ?></title>
					</xsl:when>
					<xsl:otherwise>
						<title><?php echo esc_html( $title ); ?></title>
					</xsl:otherwise>
				</xsl:choose>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
					body {
						font-size: 14px;
						font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
						margin: 0;
						color: #545353;
					}
					a {
						color: #05809e;
						text-decoration: none;
					}
					h1 {
						font-size: 24px;
						font-family: Verdana,Geneva,sans-serif;
						font-weight: normal;
						margin: 0;
					}

					#description {
						background-color: #4275f4;
						padding: 20px 40px;
						color: #fff;
						padding: 30px 30px 20px;
					}
					#description h1,
					#description p,
					#description a {
						color: #fff;
						margin: 0;
						font-size: 1.1em;
					}
					#description h1 {
						font-size: 2em;
						margin-bottom: 1em;
					}
					#description p {
						margin-top: 5px;
					}
					#description a {
						border-bottom: 1px dotted;
					}

					#content {
						padding: 20px 30px;
						background: #fff;
						max-width: 75%;
						margin: 0 auto;
					}

					table {
						border: none;
						border-collapse: collapse;
						font-size: .9em;
						width: 100%;
					}
					th {
						background-color: #4275f4;
						color: #fff;
						text-align: left;
						padding: 15px;
						font-size: 14px;
						cursor: pointer;
					}
					td {
						padding: 10px;
						border-bottom: 1px solid #ddd;
					}
					tbody tr:nth-child(even) {
						background-color: #f7f7f7;
					}
					table td a {
						display: block;
					}
					table td a img {
						max-height: 30px;
						margin: 6px 3px;
					}
				</style>
			</head>
			<body>

				<xsl:choose>
					<xsl:when test="kml:kml">

						<div id="description">

							<h1><?php esc_html_e( 'KML File', 'rank-math' ); ?></h1>

							<?php if ( false === $this->do_filter( 'sitemap/remove_credit', false ) ) : ?>
								<p>
									<?php
									printf(
										wp_kses_post(
											/* translators: link to rankmath.com */
											__( 'This KML File is generated by <a href="%s" target="_blank">Rank Math WordPress SEO Plugin</a>. It is used to provide location information to Google.', 'rank-math' )
										),
										'https://s.rankmath.com/home'
									);
									?>
								</p>
							<?php endif; ?>

							<p>
								<?php
								printf(
									wp_kses_post(
										/* translators: link to rankmath.com */
										__( 'Learn more about <a href="%s" target="_blank">KML File</a>.', 'rank-math' )
									),
									'https://developers.google.com/kml/documentation/'
								);
								?>
							</p>

						</div>

						<div id="content">
							<p class="expl">
								This KML file contains <xsl:value-of select="count(kml:kml/kml:Document/kml:Folder/kml:Placemark)"/> Locations.
							</p>
							<p class="expl">
								<?php
								printf(
									/* translators: xsl value count */
									__( '<a href="%s">&#8592; Sitemap Index</a>', 'rank-math' ),
									esc_url( Router::get_base_url( 'sitemap_index.xml' ) )
								);
								?>
							</p>
							<table id="sitemap" cellpadding="3">
								<thead>
									<tr>
										<th width="25%"><?php esc_html_e( 'Name', 'rank-math' ); ?></th>
										<th width="40%"><?php esc_html_e( 'Address', 'rank-math' ); ?></th>
										<th width="15%"><?php esc_html_e( 'Phone number', 'rank-math' ); ?></th>
										<th width="10%"><?php esc_html_e( 'Latitude', 'rank-math' ); ?></th>
										<th width="10%"><?php esc_html_e( 'Longitude', 'rank-math' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
									<xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
									<xsl:for-each select="kml:kml/kml:Document/kml:Folder/kml:Placemark">
										<tr>
											<td>
												<xsl:variable name="itemURL">
													<xsl:value-of select="atom:link/@href"/>
												</xsl:variable>
												<a href="{$itemURL}">
													<xsl:value-of select="kml:name"/>
												</a>
											</td>
											<td>
												<xsl:value-of select="kml:address"/>
											</td>
											<td>
												<xsl:value-of select="kml:phoneNumber"/>
											</td>
											<td>
												<xsl:value-of select="kml:LookAt/kml:latitude"/>
											</td>
											<td>
												<xsl:value-of select="kml:LookAt/kml:longitude"/>
											</td>
										</tr>
									</xsl:for-each>
								</tbody>
							</table>
						</div>
					</xsl:when>
					<xsl:otherwise>

						<div id="description">

							<h1><?php esc_html_e( 'XML Sitemap', 'rank-math' ); ?></h1>

							<?php if ( false === $this->do_filter( 'sitemap/remove_credit', false ) ) : ?>
								<p>
									<?php
									printf(
										wp_kses_post(
											/* translators: link to rankmath.com */
											__( 'This XML Sitemap is generated by <a href="%s" target="_blank">Rank Math WordPress SEO Plugin</a>. It is what search engines like Google use to crawl and re-crawl posts/pages/products/images/archives on your website.', 'rank-math' )
										),
										'https://s.rankmath.com/home'
									);
									?>
								</p>
							<?php endif; ?>

							<p>
								<?php
								printf(
									wp_kses_post(
										/* translators: link to rankmath.com */
										__( 'Learn more about <a href="%s" target="_blank">XML Sitemaps</a>.', 'rank-math' )
									),
									'http://sitemaps.org'
								);
								?>
							</p>

						</div>

						<div id="content">
							<xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &gt; 0">

								<p>
									<?php
									printf(
										/* translators: xsl value count */
										__( 'This XML Sitemap Index file contains <strong>%s</strong> sitemaps.', 'rank-math' ),
										'<xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/>'
									);
									?>
								</p>

								<table id="sitemap" cellpadding="3">

									<thead>
										<tr>
											<th width="75%"><?php esc_html_e( 'Sitemap', 'rank-math' ); ?></th>
											<th width="25%"><?php esc_html_e( 'Last Modified', 'rank-math' ); ?></th>
										</tr>
									</thead>

									<tbody>
										<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
											<xsl:variable name="sitemapURL">
												<xsl:value-of select="sitemap:loc"/>
											</xsl:variable>
											<tr>
												<td>
													<a href="{$sitemapURL}"><xsl:value-of select="sitemap:loc"/></a>
												</td>
												<td>
													<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)),concat(' ', substring(sitemap:lastmod,20,6)))"/>
												</td>
											</tr>
										</xsl:for-each>
									</tbody>

								</table>

							</xsl:if>
							<xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &lt; 1">
								<p>
									<?php
									printf(
										/* translators: xsl value count */
										__( 'This XML Sitemap contains <strong>%s</strong> URLs.', 'rank-math' ),
										'<xsl:value-of select="count(sitemap:urlset/sitemap:url)"/>'
									);
									?>
								</p>

								<p class="expl">
									<?php
									printf(
										/* translators: xsl value count */
										__( '<a href="%s">&#8592; Sitemap Index</a>', 'rank-math' ),
										esc_url( Router::get_base_url( 'sitemap_index.xml' ) )
									);
									?>
								</p>

								<table id="sitemap" cellpadding="3">

									<thead>
										<tr>
											<th width="80%"><?php esc_html_e( 'URL', 'rank-math' ); ?></th>
											<?php if ( Helper::get_settings( 'sitemap.include_images' ) ) : // phpcs:ignore ?><th width="5%"><?php esc_html_e( 'Images', 'rank-math' ); ?></th><?php endif; ?>
											<th title="Last Modification Time" width="15%"><?php esc_html_e( 'Last Mod.', 'rank-math' ); ?></th>
										</tr>
									</thead>

									<tbody>
										<xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
										<xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
										<xsl:for-each select="sitemap:urlset/sitemap:url">
											<tr>
												<td>
													<xsl:variable name="itemURL">
														<xsl:value-of select="sitemap:loc"/>
													</xsl:variable>
													<a href="{$itemURL}">
														<xsl:value-of select="sitemap:loc"/>
													</a>
												</td>
												<?php if ( Helper::get_settings( 'sitemap.include_images' ) ) : ?>
												<td>
													<xsl:value-of select="count(image:image)"/>
												</td>
												<?php endif; ?>
												<td>
													<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)),concat(' ', substring(sitemap:lastmod,20,6)))"/>
												</td>
											</tr>
										</xsl:for-each>
									</tbody>

								</table>

							</xsl:if>
						</div>
					</xsl:otherwise>
				</xsl:choose>

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
