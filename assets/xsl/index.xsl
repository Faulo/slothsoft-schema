<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sfs="http://schema.slothsoft.net/farah/sitemap">
	
	<xsl:template match="/*">
		<html>
			<head>
				<title>Slothsoft Schema Index</title>
				<style type="text/css"><![CDATA[
			]]></style>
			</head>
			<body>
				<main>
					<h1>Slothsoft Schema Index</h1>
					<xsl:apply-templates select="*[@name='sites']/*" mode="navi"/>
				</main>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="sfs:domain | sfs:page" mode="navi">
		<xsl:choose>
			<xsl:when test="@ref">
				<a href="{@uri}"><xsl:value-of select="@name"/></a>
			</xsl:when>
			<xsl:otherwise>
				<span><xsl:value-of select="@name"/></span>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="*">
			<ul>
				<xsl:for-each select="sfs:page">
					<li><xsl:apply-templates select="." mode="navi"/></li>
				</xsl:for-each>
			</ul>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>