<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:ssv="http://schema.slothsoft.net/schema/versioning"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions"
	xmlns:str="http://exslt.org/strings" xmlns:lio="http://slothsoft.net" extension-element-prefixes="exsl func str lio" xmlns:php="http://php.net/xsl">

	<xsl:include href="farah://slothsoft@farah/xsl/module" />

	<func:function name="lio:getDescendantElements">
		<xsl:param name="rootNode" />
		<xsl:variable name="idList" select="str:tokenize(lio:getDescendantElementIds($rootNode))" />
		<func:result select="$manifest/element[@id = $idList]" />
	</func:function>
	<func:function name="lio:getDescendantElementIds">
		<xsl:param name="rootNode" />
		<xsl:param name="stack" select="/.." />

		<func:result>
			<xsl:for-each select="$rootNode[not(@id = $stack/@id)]">
				<xsl:value-of select="concat(@id, ' ')" />
				<xsl:value-of select="lio:getDescendantElementIds($manifest/element[@id = current()//elementReference/@id], $stack | .)" />
			</xsl:for-each>
		</func:result>
	</func:function>

	<func:function name="lio:getDescendantTypes">
		<xsl:param name="rootNode" />
		<xsl:variable name="idList" select="str:tokenize(lio:getDescendantTypeIds($rootNode))" />
		<func:result select="$manifest/type[@id = $idList]" />
	</func:function>
	<func:function name="lio:getDescendantTypeIds">
		<xsl:param name="rootNode" />
		<xsl:param name="stack" select="/.." />

		<func:result>
			<xsl:for-each select="$manifest/type[not(@id = $stack/@id)][@id = $rootNode//typeReference/@id]">
				<xsl:value-of select="concat(@id, ' ')" />
				<xsl:value-of select="lio:getDescendantTypeIds(., $stack | .)" />
			</xsl:for-each>
		</func:result>
	</func:function>

	<xsl:variable name="infoList" select="/*/*[@name='schema-info']/*/ssv:info" />
	<xsl:variable name="info" select="$infoList[1]" />

	<xsl:variable name="manifestList" select="/*/*[@name='schema-manifest']/*/manifest" />
	<xsl:variable name="manifest" select="$manifestList[1]" />

	<xsl:template match="/*">
		<html>
			<head>
				<title>
					<xsl:apply-templates select="$info" mode="title" />
				</title>
			</head>
			<body>
				<header>
					<xsl:apply-templates select="$info" mode="header" />
				</header>
				<main>
					<xsl:apply-templates select="sfm:error" mode="sfm:html" />
					<xsl:apply-templates select="$info" mode="body" />
				</main>
				<footer>
					<xsl:apply-templates select="$info" mode="footer" />
				</footer>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="ssv:info" mode="header">
		<h1>
			<xsl:apply-templates select="." mode="title" />
		</h1>
		<p>
			<span data-dict=".">header/1</span>
			<a href="{ssv:namespace}">
				<code>
					<xsl:value-of select="ssv:namespace" />
				</code>
			</a>
			<span data-dict=".">header/2</span>
			<code>
				<xsl:value-of select="ssv:prefix" />
			</code>
			<span data-dict=".">header/3</span>
		</p>
	</xsl:template>

	<xsl:template match="ssv:info" mode="footer">
		<div>
			<span data-dict=".">footer/copyright</span>
			<xsl:for-each select="ssv:author">
				<a href="mailto:{@email}">
					<xsl:value-of select="." />
				</a>
				,
			</xsl:for-each>
			<span data-dict=".">footer/company</span>
		</div>
	</xsl:template>

	<xsl:template match="*" mode="title">
		<a href="/">
			<span data-dict=".">title</span>
		</a>
		<xsl:apply-templates select="." mode="name" />
	</xsl:template>

	<xsl:template match="*" mode="name">
		<xsl:value-of select="concat(ssv:name, ' v', ssv:version, ' ', ssv:revision)" />
	</xsl:template>

	<xsl:template match="*" mode="changelog">
		<p class="changelog">
			<xsl:value-of select="php:function('trim', string(ssv:changelog))" />
		</p>
	</xsl:template>
</xsl:stylesheet>
