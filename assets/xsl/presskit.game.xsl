<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:sfs="http://schema.slothsoft.net/farah/sitemap" xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:svg="http://www.w3.org/2000/svg" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssp="http://schema.slothsoft.net/schema/presskit">

	<xsl:variable name="company" select="/*/*[@name='company']/ssp:company" />
	<xsl:variable name="game" select="/*/*[@name='game']/ssp:game" />
	<xsl:variable name="images" select="/*/*[@name='images']/*/*" />
	<xsl:variable name="logos" select="/*/*[@name='logos']/*/*" />
	<xsl:variable name="header" select="/*/*[@name='header']/*/*" />

	<xsl:variable name="trailers" select="$game/ssp:trailers/*" />
	<xsl:variable name="awards" select="$game/ssp:awards/*" />
	<xsl:variable name="quotes" select="$game/ssp:quotes/*" />
	<xsl:variable name="additionals" select="$game/ssp:additionals/*" />
	<xsl:variable name="credits" select="$game/ssp:credits/*" />

	<xsl:variable name="contacts" select="$company/ssp:contacts/*" />
	<xsl:variable name="socials" select="$company/ssp:socials/*" />

	<xsl:variable name="platforms" select="$game/ssp:platforms/*" />
	<xsl:variable name="prices" select="$game/ssp:prices/*" />
	<xsl:variable name="thanks" select="$game/ssp:thanks/*" />

	<xsl:template match="/*">
		<div class="uk-container uk-container-center">
			<div class="uk-grid">
				<div id="navigation" class="uk-width-medium-1-4">
					<h1 class="nav-header">
						<xsl:value-of select="$company/ssp:title" />
					</h1>
					<ul class="uk-nav uk-nav-side">
						<li>
							<a href="#factsheet">Factsheet</a>
						</li>
						<li>
							<a href="#description">Description</a>
						</li>
						<li>
							<a href="#history">History</a>
						</li>
						<li>
							<a href="#features">Features</a>
						</li>
						<li>
							<a href="#trailers">Videos</a>
						</li>
						<li>
							<a href="#images">Images</a>
						</li>
						<li>
							<a href="#logo">Logo &amp; Icon</a>
						</li>
						<xsl:if test="$awards">
							<li>
								<a href="#awards">Awards &amp; Recognition</a>
							</li>
						</xsl:if>
						<xsl:if test="$quotes">
							<li>
								<a href="#quotes">Selected Articles</a>
							</li>
						</xsl:if>
						<xsl:if test="$additionals">
							<li>
								<a href="#links">Additional Links</a>
							</li>
						</xsl:if>
						<li>
							<a href="#about">
								About
								<xsl:value-of select="$company/ssp:title" />
							</a>
						</li>
						<xsl:if test="$credits">
							<li>
								<a href="#credits">Team</a>
							</li>
						</xsl:if>
						<xsl:if test="$contacts">
							<li>
								<a href="#contact">Contact</a>
							</li>
						</xsl:if>
					</ul>
				</div>
				<div id="content" class="uk-width-medium-3-4">
					<xsl:for-each select="$header">
						<img src="{@href}" alt="{@name}" class="header" />
					</xsl:for-each>


					<div class="uk-grid">
						<div class="uk-width-medium-2-6">
							<h2 id="factsheet">Factsheet</h2>
							<p>
								<strong>Game:</strong>
								<br />
								<xsl:value-of select="$game/ssp:title" />
							</p>
							<p>
								<strong>Developer:</strong>
								<br />
								<a href="{$company/website}">
									<xsl:value-of select="$company/ssp:title" />
								</a>
								<br />
								Based in
								<xsl:value-of select="$company/ssp:based-in" />
							</p>
							<p>
								<strong>Release date:</strong>
								<br />
								<xsl:value-of select="$game/ssp:release-date" />
							</p>
							<xsl:if test="$platforms">
								<p>
									<strong>Platforms:</strong>
									<br />
									<xsl:for-each select="$platforms">
										<a href="{link}">
											<xsl:value-of select="ssp:name" />
										</a>
										<br />
									</xsl:for-each>
								</p>
							</xsl:if>
							<p>
								<strong>Website:</strong>
								<br />
								<a href="{$game/website}">
									<xsl:value-of select="$game/ssp:website" />
								</a>
							</p>
							<xsl:if test="$prices">
								<p>
									<strong>Regular Price:</strong>
									<br />
									<xsl:for-each select="$prices">
										<a href="{link}">
											<xsl:value-of select="ssp:value" />
										</a>
										<br />
									</xsl:for-each>
								</p>
							</xsl:if>
						</div>
						<div class="uk-width-medium-4-6">
							<h2 id="description">Description</h2>
							<xsl:for-each select="$game/ssp:description">
								<xsl:copy-of select="node()" />
							</xsl:for-each>

							<h2 id="history">History</h2>
							<xsl:for-each select="$game/ssp:history">
								<xsl:copy-of select="node()" />
							</xsl:for-each>

							<h2 id="features">Features</h2>
							<ul>
								<xsl:for-each select="$game/ssp:features/*">
									<li>
										<xsl:copy-of select="node()" />
									</li>
								</xsl:for-each>
							</ul>
						</div>
					</div>

					<hr />

					<h2 id="trailers">Videos</h2>

					<xsl:choose>
						<xsl:when test="$trailers">
							<xsl:for-each select="$trailers">
								<p>
									<strong>
										<xsl:value-of select="ssp:name" />
									</strong>
									<xsl:text> </xsl:text>
									<a href="https://www.youtube.com/watch?v={youtube}" rel="external" target="_blank">YouTube</a>
								</p>
								<div class="uk-responsive-width iframe-container">
									<iframe src="https://www.youtube.com/embed/{youtube}" allowfullscreen="" frameborder="0" />
								</div>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<p class="trailers-text">
								There are currently no trailers available for
								<xsl:value-of select="$game/ssp:title" />
								. Check back later for more or
								<a href="#contact">contact us</a>
								for specific requests!
							</p>
						</xsl:otherwise>
					</xsl:choose>

					<hr />

					<h2 id="images">Images</h2>
					<xsl:choose>
						<xsl:when test="$images">
							<div class="uk-grid images">
								<xsl:for-each select="$images">
									<div class="uk-width-medium-1-2">
										<a href="{@href}">
											<img src="{@href}" alt="{@name}" />
										</a>
									</div>
								</xsl:for-each>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<p class="images-text">
								There are currently no screenshots available for
								<xsl:value-of select="$game/ssp:title" />
								. Check back later for more or
								<a href="#contact">contact us</a>
								for specific requests!
							</p>
						</xsl:otherwise>
					</xsl:choose>
					<hr />

					<h2 id="logo">Logo &amp; Icon</h2>
					<xsl:choose>
						<xsl:when test="$logos">
							<div class="uk-grid images">
								<xsl:for-each select="$logos">
									<div class="uk-width-medium-1-2">
										<a href="{@href}">
											<img src="{@href}" alt="{@name}" />
										</a>
									</div>
								</xsl:for-each>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<p>
								There are currently no logos or icons available for
								<xsl:value-of select="$game/ssp:title" />
								. Check back later for more or
								<a href="#contact">contact us</a>
								for specific requests!
							</p>
						</xsl:otherwise>
					</xsl:choose>
					<hr />

					<xsl:if test="$awards">
						<h2 id="awards">Awards &amp; Recognition</h2>
						<hr />
					</xsl:if>

					<xsl:if test="$quotes">
						<h2 id="quotes">Selected Articles</h2>
						<hr />
					</xsl:if>


					<xsl:if test="$additionals">
						<h2 id="links">Additional Links</h2>
						<xsl:for-each select="$additionals">
							<p>
								<strong>
									<xsl:value-of select="ssp:title" />
								</strong>
								<br />
								<a href="{ssp:link}">
									<xsl:copy-of select="ssp:description/node()" />
								</a>
							</p>
						</xsl:for-each>
						<hr />
					</xsl:if>

					<h2 id="about">
						About
						<xsl:value-of select="$company/ssp:title" />
					</h2>
					<xsl:for-each select="$company/ssp:description">
						<xsl:copy-of select="." />
					</xsl:for-each>

					<hr />

					<div class="uk-grid">
						<div class="uk-width-medium-1-2">
							<h2 id="credits">
								<xsl:value-of select="$game/ssp:title" />
								Credits
							</h2>
							<xsl:for-each select="$credits">
								<p>
									<strong>
										<xsl:value-of select="ssp:person" />
									</strong>
									<br />
									<a href="{website}">
										<xsl:value-of select="ssp:role" />
									</a>
								</p>
							</xsl:for-each>
						</div>
						<div class="uk-width-medium-1-2">
							<h2 id="contact">Contact</h2>
							<xsl:for-each select="$contacts">
								<p>
									<strong>
										<xsl:value-of select="ssp:name" />
									</strong>
									<br />
									<xsl:if test="ssp:mail">


										<a href="mailto:{ssp:mail}">
											<xsl:value-of select="ssp:mail" />
										</a>
									</xsl:if>
									<xsl:if test="ssp:link">


										<a href="{ssp:link}" rel="external">
											<xsl:value-of select="ssp:link" />
										</a>
									</xsl:if>
								</p>
							</xsl:for-each>
						</div>
					</div>

					<hr />

					<xsl:if test="$thanks">
						<h2 id="about">Special Thanks to our Playtesters</h2>
						<xsl:for-each select="$thanks">
							<xsl:sort select="substring-after(ssp:person, ' ')" />
							<xsl:if test="position() > 1">
								<xsl:text>, </xsl:text>
							</xsl:if>
							<span>
								<xsl:value-of select="ssp:person" />
							</span>
						</xsl:for-each>
						<hr />
					</xsl:if>

					<p>
						<a href="http://dopresskit.com/">presskit()</a>
						<xsl:text> by Rami Ismail (</xsl:text>
						<a href="https://www.vlambeer.com/">Vlambeer</a>
						<xsl:text>) - also thanks to </xsl:text>
						<a href="https://www.vlambeer.com/press/sheet.php?p=credits">these fine folks</a>
					</p>
				</div>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>