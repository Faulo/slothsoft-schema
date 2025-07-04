<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:sfs="http://schema.slothsoft.net/farah/sitemap" xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:svg="http://www.w3.org/2000/svg" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssp="http://schema.slothsoft.net/schema/presskit" xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
	xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:str="http://exslt.org/strings" extension-element-prefixes="func php" xmlns:php="http://php.net/xsl">

	<xsl:template name="ssp:date">
		<xsl:param name="date" select="." />
		<xsl:param name="format" select="'d.m.Y'" />

		<xsl:variable name="text" select="normalize-space($date)" />
		<xsl:variable name="datetime" select="normalize-space($date/@datetime)" />

		<xsl:choose>
			<xsl:when test="concat($text, $datetime) != ''">
				<time>
					<xsl:if test="$datetime != ''">
						<xsl:attribute name="datetime"><xsl:value-of select="$datetime" /></xsl:attribute>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="$text = ''">
							<xsl:value-of select="php:function('date', $format, php:function('strtotime', $datetime))" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="$text" />
						</xsl:otherwise>
					</xsl:choose>
				</time>
			</xsl:when>
			<xsl:otherwise>
				<code>ssp:date requires a "datetime" attribute or text content.</code>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>