<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:ssv="http://schema.slothsoft.net/schema/versioning"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common"
	xmlns:func="http://exslt.org/functions"
	xmlns:str="http://exslt.org/strings"
	xmlns:ibp="http://www.ibp-dresden.de"
	extension-element-prefixes="exsl func str ibp">
	
	<func:function name="ibp:getDescendantElements">
		<xsl:param name="rootNode"/>
		<xsl:variable name="idList" select="str:tokenize(ibp:getDescendantElementIds($rootNode))"/>
		<func:result select="$manifest/element[@id = $idList]"/>
	</func:function>
	<func:function name="ibp:getDescendantElementIds">
		<xsl:param name="rootNode"/>
		<xsl:param name="stack" select="/.."/>
		
		<func:result>
			<xsl:for-each select="$rootNode[not(@id = $stack/@id)]">
				<xsl:value-of select="concat(@id, ' ')"/>
				<xsl:value-of select="ibp:getDescendantElementIds($manifest/element[@id = current()//elementReference/@id], $stack | .)"/>
			</xsl:for-each>
		</func:result>
	</func:function>
	
	<func:function name="ibp:getDescendantTypes">
		<xsl:param name="rootNode"/>
		<xsl:variable name="idList" select="str:tokenize(ibp:getDescendantTypeIds($rootNode))"/>
		<func:result select="$manifest/type[@id = $idList]"/>
	</func:function>
	<func:function name="ibp:getDescendantTypeIds">
		<xsl:param name="rootNode"/>
		<xsl:param name="stack" select="/.."/>
		
		<func:result>
			<xsl:for-each select="$manifest/type[not(@id = $stack/@id)][@id = $rootNode//typeReference/@id]">
				<xsl:value-of select="concat(@id, ' ')"/>
				<xsl:value-of select="ibp:getDescendantTypeIds(., $stack | .)"/>
			</xsl:for-each>
		</func:result>
	</func:function>
	
	<xsl:variable name="config" select="/data/config"/>
	<xsl:variable name="schemaList" select="/*/*[@name='schema-manifest']/*"/>
	
	<xsl:variable name="schema-info" select="//ssv:manifest"/>
	<xsl:variable name="latest" select="$schema-info[1]"/>
	
	<xsl:variable name="schema" select="$schemaList[@active]"/>
	
	<xsl:variable name="manifest" select="$schemaList[1]//manifest"/>
	
	<xsl:template match="/*">
		<html>
			<head>
				<title><xsl:apply-templates select="$latest" mode="title"/></title>
			</head>
			<body>
				<header>
					<xsl:apply-templates select="." mode="header"/>
				</header>
				<main>
					<xsl:apply-templates select="." mode="body"/>
				</main>
				<footer>
					<xsl:apply-templates select="." mode="footer"/>
				</footer>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="*" mode="header">
		<h1><xsl:apply-templates select="$latest" mode="title"/></h1>
		<p>
			This document describes the namespace <a href="{$latest/ssv:namespace}"><code><xsl:value-of select="$latest/ssv:namespace"/></code></a>, conventionally prefixed as <code><xsl:value-of select="$latest/ssv:prefix"/></code>.
		</p>
	</xsl:template>
	
	<xsl:template match="*" mode="body">	
	</xsl:template>
	
	<xsl:template match="*" mode="footer">
		<div>
			© 2017, 2018 
			<a href="mailto:{$latest/ssv:author/@email}"><xsl:value-of select="$latest/ssv:author"/></a>
		</div>
	</xsl:template>
	
	<xsl:template match="*" mode="title">
		<xsl:value-of select="concat('Slothsoft Schema: ', ssv:name, ' v', ssv:version, ' ', ssv:revision)"/>
	</xsl:template>
</xsl:stylesheet>
