<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:ssv="http://schema.slothsoft.net/schema/versioning"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:ibp="http://www.ibp-dresden.de"
	extension-element-prefixes="ibp">
	
	<xsl:include href="farah://slothsoft@schema/xsl/schema.global"/>
	
	<xsl:variable name="tocElementLimit" select="2500"/>
	
	<xsl:variable name="documentationURI" select="concat($config/@uri-base, $config/@uri-documentation, '&amp;schema=', $schema/@id)"/>
	
	<xsl:variable name="rootElementList" 			select="$manifest/element[@isRoot]"/>
	<xsl:variable name="elementDefinitionList" 		select="ibp:getDescendantElements($rootElementList)"/>
	<xsl:variable name="elementReferenceList" 		select="$elementDefinitionList//elementReference"/>
	
	<xsl:variable name="attributeReferenceList" 	select="$elementDefinitionList//attributeReference"/>
	<xsl:variable name="attributeDefinitionList" 	select="$manifest/attribute[@id = $attributeReferenceList/@id]"/>
	<xsl:variable name="rootAttributeList" 			select="$attributeDefinitionList[@isRoot]"/>
	
	<xsl:variable name="typeDefinitionList" 		select="ibp:getDescendantTypes($elementDefinitionList | $attributeDefinitionList)"/>
	<xsl:variable name="rootTypeList" 				select="$typeDefinitionList[@name]"/>
	
	<xsl:variable name="categoryDefinitionList" 	select="$manifest/category"/>
	<xsl:variable name="rootCategoryList" 			select="$categoryDefinitionList[@name]"/>
	
	<xsl:variable name="annotationDefinitionList" 	select="$manifest/annotation"/>
	<xsl:variable name="rootAnnotationList" 		select="$annotationDefinitionList[not(@id = $manifest//annotationReference/@id)]"/>
	
	
	
	
	<xsl:template match="*" mode="header">
		<h1><xsl:apply-templates select="$latest" mode="title"/></h1>
		<p>
			This document describes the namespace <a href="{$latest/ssv:namespace}"><code><xsl:value-of select="$latest/ssv:namespace"/></code></a>, conventionally prefixed as <code><xsl:value-of select="$latest/ssv:prefix"/></code>.
		</p>
		<xsl:apply-templates select="$rootAnnotationList" mode="content"/>
	</xsl:template>
	
	<xsl:template match="*" mode="nav">
	</xsl:template>
	
	<xsl:template match="*" mode="body">
		<nav>
			<details>
				<summary><h2>Table of contents</h2></summary>
				<xsl:apply-templates select="$manifest" mode="toc"/>
			</details>
		</nav>
		<xsl:apply-templates select="$manifest" mode="content"/>
	</xsl:template>
	
	<!-- Table of Contents -->
	<xsl:template match="manifest" mode="toc">
		<ul class="toc">
			<xsl:if test="count($elementDefinitionList)">
				<li>
					<a href="#{generate-id(.)}-elements">Elemente im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></a>
					<ul>
						<xsl:choose>
							<xsl:when test="count($rootElementList) &gt; 0 and count($elementReferenceList) &lt; $tocElementLimit">
								<xsl:apply-templates select="$rootElementList" mode="toc"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:for-each select="$elementDefinitionList">
									<!--<xsl:sort select="@sort"/>-->
									<li><a href="#{@href}">Das Element <code class="element"><xsl:value-of select="@name"/></code></a></li>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
					</ul>
				</li>
			</xsl:if>
			<xsl:if test="count($attributeDefinitionList)">
				<li>
					<a href="#{generate-id(.)}-attributes">Attribute im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></a>
					<ul>
						<xsl:for-each select="$attributeDefinitionList">
							<!--<xsl:sort select="@sort"/>-->
							<li><a href="#{@href}">Das Attribute <code class="attribute"><xsl:value-of select="@name"/></code></a></li>
						</xsl:for-each>
					</ul>
				</li>
			</xsl:if>
			<xsl:if test="count($rootTypeList)">
				<li>
					<a href="#{generate-id(.)}-types">Content Models im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></a>
					<ul>
						<xsl:for-each select="$rootTypeList">
							<xsl:sort select="@sort"/>
							<li><a href="#{@href}">Das Content Model <code class="type"><xsl:value-of select="@name"/></code></a></li>
						</xsl:for-each>
					</ul>
				</li>
			</xsl:if>
			<xsl:if test="count($rootCategoryList)">
				<li>
					<a href="#{generate-id(.)}-categories">Kategorien im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></a>
					<ul>
						<xsl:for-each select="$rootCategoryList">
							<xsl:sort select="@sort"/>
							<li><a href="#{@href}">Die Kategorie <code class="category"><xsl:value-of select="@name"/></code></a></li>
						</xsl:for-each>
					</ul>
				</li>
			</xsl:if>
		</ul>
	</xsl:template>
	<xsl:template match="element" mode="toc">
		<xsl:param name="stack" select="/.."/>
		<xsl:variable name="currentNode" select="."/>
		<xsl:variable name="childElementNodeList" select="$elementDefinitionList[@id = $currentNode//elementReference/@id]"/>
		
		<xsl:if test="count($stack[@id = $currentNode/@id]) = 0">
			<li>
				<xsl:apply-templates select="." mode="href"/>
				<xsl:if test="count($childElementNodeList) and count($childElementNodeList[@id = $currentNode/@id]) = 0">
					<ul>
						<xsl:apply-templates select="$childElementNodeList" mode="toc">
							<xsl:with-param name="stack" select="$stack | $currentNode"/>
						</xsl:apply-templates>
					</ul>
				</xsl:if>
			</li>
		</xsl:if>
	</xsl:template>
	
	
	
	<!-- Content -->
	<xsl:template match="manifest" mode="content">
		<xsl:if test="count($elementDefinitionList)">
			<h2 id="{generate-id(.)}-elements">Elemente im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></h2>
			<xsl:apply-templates select="$elementDefinitionList" mode="content">
				<!--<xsl:sort select="@sort"/>-->
			</xsl:apply-templates>
		</xsl:if>
		<xsl:if test="count($attributeDefinitionList)">
			<h2 id="{generate-id(.)}-attributes">Attribute im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></h2>
			<xsl:apply-templates select="$attributeDefinitionList" mode="content">
				<!--<xsl:sort select="@sort"/>-->
			</xsl:apply-templates>
		</xsl:if>
		<xsl:if test="count($rootTypeList)">
			<h2 id="{generate-id(.)}-types">Content Models im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></h2>
			<xsl:apply-templates select="$rootTypeList" mode="content">
				<xsl:sort select="@sort"/>
			</xsl:apply-templates>
		</xsl:if>
		<xsl:if test="count($rootCategoryList)">
			<h2 id="{generate-id(.)}-categories">Kategorien im Namensraum <code class="namespace"><xsl:value-of select="$config/@namespace"/></code></h2>
			<xsl:apply-templates select="$rootCategoryList" mode="content">
				<xsl:sort select="@sort"/>
			</xsl:apply-templates>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="element" mode="content">
		<xsl:variable name="currentNode" select="."/>
		
		<xsl:variable name="parentElementNodeList" select="$elementDefinitionList[.//elementReference/@id = $currentNode/@id]"/>
		<xsl:variable name="childElementNodeList" select=".//childNamespace | .//elementReference"/>
		<xsl:variable name="attributeNodeList" select="$attributeDefinitionList[@id = $currentNode//attributeReference/@id]"/>
		
		<xsl:variable name="parentTypeNodeList" select="$rootTypeList[@id = ($currentNode//typeReference[@id = $rootTypeList/@id])[1]/@id]"/>
		<xsl:variable name="foreignTypeNodeList" select=".//foreignType"/>
		<xsl:variable name="typeNodeList" select="$parentTypeNodeList | $foreignTypeNodeList[count($parentTypeNodeList) = 0]"/>
		
		<xsl:variable name="categoryNodeList" select="$categoryDefinitionList[@id = $currentNode//categoryReference/@id]"/>
		<xsl:variable name="annotationNodeList" select="$annotationDefinitionList[@id = $currentNode//annotationReference/@id]"/>
		<xsl:variable name="patternNodeList" select=".//pattern"/>
		<xsl:variable name="tokenNodeList" select=".//token"/>
		
		<h3 id="{@href}">Das Element <code class="element"><xsl:value-of select="@name"/></code></h3>
		<dl class="element">
			<xsl:if test="count($categoryNodeList)">
				<dt>Kategorie:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$categoryNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@isRoot">
				</xsl:when>
				<xsl:otherwise>
					<dt>Kontext, in welchem dieses Element vorkommen kann:</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="@isRoot">
								<i>Wurzelelement</i>
							</xsl:when>
							<xsl:when test="count($parentElementNodeList)">
								<ul>
									<xsl:for-each select="$parentElementNodeList">
										<li>
											<xsl:apply-templates select="." mode="href"/>
										</li>
									</xsl:for-each>
								</ul>
							</xsl:when>
							<xsl:otherwise>
								-
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="count($typeNodeList)">
				<dt>Content Model:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$typeNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<dt>Mögliche Kind-Elemente:</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="count($childElementNodeList)">
						<ul>
							<xsl:for-each select="$childElementNodeList">
								<li>
									<xsl:apply-templates select="." mode="href"/>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						-
					</xsl:otherwise>
				</xsl:choose>
			</dd>
			<dt>Mögliche Attribute:</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="count($attributeNodeList)">
						<ul>
							<xsl:for-each select="$attributeNodeList">
								<li>
									<xsl:apply-templates select="." mode="inline"/>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						-
					</xsl:otherwise>
				</xsl:choose>
			</dd>
			<xsl:if test="count($patternNodeList)">
				<dt>Erlaubtes Muster:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$patternNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($tokenNodeList)">
				<dt>Mögliche Werte:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$tokenNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
		</dl>
		<xsl:apply-templates select="$annotationNodeList" mode="content"/>
	</xsl:template>
	
	<xsl:template match="attribute" mode="content">
		<xsl:variable name="currentNode" select="."/>
		
		<xsl:variable name="parentTypeNodeList" select="$rootTypeList[@id = ($currentNode//typeReference[@id = $rootTypeList/@id])[1]/@id]"/>
		<xsl:variable name="foreignTypeNodeList" select=".//foreignType"/>
		<xsl:variable name="typeNodeList" select="$parentTypeNodeList | $foreignTypeNodeList[count($parentTypeNodeList) = 0]"/>
		
		<xsl:variable name="ownerElementNodeList" select="$elementDefinitionList[.//attributeReference/@id = $currentNode/@id]"/>
		<xsl:variable name="categoryNodeList" select="$categoryDefinitionList[@id = $currentNode//categoryReference/@id]"/>
		<xsl:variable name="annotationNodeList" select="$annotationDefinitionList[@id = $currentNode//annotationReference/@id]"/>
		<xsl:variable name="patternNodeList" select=".//pattern"/>
		<xsl:variable name="tokenNodeList" select=".//token"/>
		
		<h3 id="{@href}">Das Attribut <code class="attribute"><xsl:value-of select="@name"/></code></h3>
		<dl class="attribute">
			<xsl:if test="count($categoryNodeList)">
				<dt>Kategorie:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$categoryNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<dt>Erforderlich:</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="$currentNode/@isRequired = '1'">
						Ja
					</xsl:when>
					<xsl:otherwise>
						Nein
					</xsl:otherwise>
				</xsl:choose>
			</dd>
			<dt>Elemente, die dieses Attribut verwenden:</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="count($ownerElementNodeList)">
						<ul>
							<xsl:for-each select="$ownerElementNodeList">
								<li>
									<xsl:apply-templates select="." mode="href"/>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						-
					</xsl:otherwise>
				</xsl:choose>
			</dd>
			<xsl:if test="count($typeNodeList)">
				<dt>Content Model:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$typeNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($patternNodeList)">
				<dt>Erlaubtes Muster:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$patternNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($tokenNodeList)">
				<dt>Mögliche Werte:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$tokenNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
		</dl>
		<xsl:apply-templates select="$annotationNodeList" mode="content"/>
	</xsl:template>
	
	<xsl:template match="type" mode="content">
		<xsl:variable name="currentNode" select="."/>
		
		<xsl:variable name="childElementNodeList" select=".//childNamespace | .//elementReference"/>
		<xsl:variable name="memberElementNodeList" select="$elementDefinitionList[.//typeReference/@id = $currentNode/@id]"/>
		<xsl:variable name="childAttributeNodeList" select="$attributeDefinitionList[@id = $currentNode//attributeReference/@id]"/>
		<xsl:variable name="memberAttributeNodeList" select="$attributeDefinitionList[.//typeReference/@id = $currentNode/@id]"/>
		<xsl:variable name="childTypeNodeList" select="$rootTypeList[.//typeReference/@id = $currentNode/@id]"/>
		
		<xsl:variable name="parentTypeNodeList" select="$rootTypeList[@id = ($currentNode//typeReference[@id = $rootTypeList/@id])[1]/@id]"/>
		<xsl:variable name="foreignTypeNodeList" select=".//foreignType"/>
		<xsl:variable name="typeNodeList" select="$parentTypeNodeList | $foreignTypeNodeList[count($parentTypeNodeList) = 0]"/>
		
		<xsl:variable name="annotationNodeList" select="$annotationDefinitionList[@id = $currentNode//annotationReference/@id]"/>
		<xsl:variable name="patternNodeList" select=".//pattern"/>
		<xsl:variable name="tokenNodeList" select=".//token"/>
		
		<h3 id="{@href}">Das Content Model <code class="type"><xsl:value-of select="@name"/></code></h3>
		<dl class="type">
			<!--
			<xsl:if test="count($memberElementNodeList) = 0 and count($memberAttributeNodeList) = 0">
				<dt>UNUSED</dt>
			</xsl:if>
			-->
			<xsl:if test="count($memberElementNodeList)">
				<dt>Elemente, die dieses Content Model verwenden:</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="count($memberElementNodeList)">
							<ul>
								<xsl:for-each select="$memberElementNodeList">
									<li>
										<xsl:apply-templates select="." mode="href"/>
									</li>
								</xsl:for-each>
							</ul>
						</xsl:when>
						<xsl:otherwise>
							-
						</xsl:otherwise>
					</xsl:choose>
				</dd>
			</xsl:if>
			<xsl:if test="count($memberAttributeNodeList)">
				<dt>Attribute, die dieses Content Model verwenden:</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="count($memberAttributeNodeList)">
							<ul>
								<xsl:for-each select="$memberAttributeNodeList">
									<li>
										<xsl:apply-templates select="." mode="href"/>
									</li>
								</xsl:for-each>
							</ul>
						</xsl:when>
						<xsl:otherwise>
							-
						</xsl:otherwise>
					</xsl:choose>
				</dd>
			</xsl:if>
			<xsl:if test="count($typeNodeList)">
				<dt>Content Model, von dem dieses Model abgeleitet ist:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$typeNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($childTypeNodeList)">
				<dt>Content Models, die dieses Model ableiten:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$childTypeNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($childElementNodeList) or count($childAttributeNodeList)">
				<dt>Mögliche Kind-Elemente:</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="count($childElementNodeList)">
							<ul>
								<xsl:for-each select="$childElementNodeList">
									<li>
										<xsl:apply-templates select="." mode="href"/>
									</li>
								</xsl:for-each>
							</ul>
						</xsl:when>
						<xsl:otherwise>
							-
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>Mögliche Attribute:</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="count($childAttributeNodeList)">
							<ul>
								<xsl:for-each select="$childAttributeNodeList">
									<li>
										<xsl:apply-templates select="." mode="inline"/>
									</li>
								</xsl:for-each>
							</ul>
						</xsl:when>
						<xsl:otherwise>
							-
						</xsl:otherwise>
					</xsl:choose>
				</dd>
			</xsl:if>
			<xsl:if test="count($patternNodeList)">
				<dt>Erlaubtes Muster:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$patternNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
			<xsl:if test="count($tokenNodeList)">
				<dt>Mögliche Werte:</dt>
				<dd>
					<ul>
						<xsl:for-each select="$tokenNodeList">
							<li>
								<xsl:apply-templates select="." mode="href"/>
							</li>
						</xsl:for-each>
					</ul>
				</dd>
			</xsl:if>
		</dl>
		<xsl:apply-templates select="$annotationNodeList" mode="content"/>
	</xsl:template>
	
	<xsl:template match="category" mode="content">
		<xsl:variable name="currentNode" select="."/>
		
		<xsl:variable name="memberElementNodeList" select="$elementDefinitionList[.//categoryReference/@id = $currentNode/@id]"/>
		<xsl:variable name="annotationNodeList" select="$annotationDefinitionList[@id = $currentNode//annotationReference/@id]"/>
		
		<h3 id="{@href}">Die Kategorie <code class="category"><xsl:value-of select="@name"/></code></h3>
		<dl class="category">
			<dt>Elemente in dieser Kategorie:</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="count($memberElementNodeList)">
						<ul>
							<xsl:for-each select="$memberElementNodeList">
								<li>
									<xsl:apply-templates select="." mode="href"/>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						-
					</xsl:otherwise>
				</xsl:choose>
			</dd>
		</dl>
		<xsl:apply-templates select="$annotationNodeList" mode="content"/>
	</xsl:template>
	
	<xsl:template match="annotation" mode="content">
		<div class="annotation">
			<xsl:for-each select="documentation">
				<div>
					<xsl:for-each select="node()">
						<xsl:copy-of select="."/>
					</xsl:for-each>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
	
	
	
	<!-- Inline -->
	<xsl:template match="attribute[@name]" mode="inline">
		<xsl:variable name="patternNodeList" select=".//pattern"/>
		<xsl:variable name="tokenNodeList" select=".//token"/>
		<xsl:variable name="foreignTypeNodeList" select=".//foreignType"/>
		<span class="BNF">
			<xsl:apply-templates select="." mode="href"/>
			<xsl:text> ::= </xsl:text>
			<xsl:choose>
				<xsl:when test="count($patternNodeList)">
					<xsl:for-each select="$patternNodeList">
						<xsl:if test="position() != 1">
							<xsl:text> | </xsl:text>
						</xsl:if>
						<xsl:apply-templates select="." mode="href"/>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="count($tokenNodeList)">
					<xsl:for-each select="$tokenNodeList">
						<xsl:if test="position() != 1">
							<xsl:text> | </xsl:text>
						</xsl:if>
						<xsl:apply-templates select="." mode="href"/>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="$foreignTypeNodeList">
						<xsl:if test="position() != 1">
							<xsl:text> | </xsl:text>
						</xsl:if>
						<xsl:apply-templates select="." mode="href"/>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</span>
	</xsl:template>
	
	<!-- Anchors -->
	<xsl:template match="*" mode="href"/>
	
	<xsl:template match="element[@name]" mode="href">
		<a href="#{@href}"><code class="element"><xsl:value-of select="@name"/></code></a>
	</xsl:template>
	<xsl:template match="elementReference" mode="href">
		<xsl:variable name="target" select="$elementDefinitionList[@id = current()/@id]"/>
		<code class="element">
			<a href="#{$target/@href}"><xsl:value-of select="$target/@name"/></a>
			<abbr>
				<xsl:attribute name="title">
					<xsl:choose>
						<xsl:when test="@min = @max">
							<xsl:value-of select="concat('Genau ', @min, '')"/>
						</xsl:when>
						<xsl:when test="@max = 'unbounded' and @min = '0'">
							<xsl:value-of select="'Beliebig viele'"/>
						</xsl:when>
						<xsl:when test="@max = 'unbounded'">
							<xsl:value-of select="concat('Mindestens ', @min, '')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="concat('Mindestens ', @min, ', höchstens ', @max, '')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:value-of select="concat('(', @cardinality, ')')"/>
			</abbr>
		</code>
	</xsl:template>
	<xsl:template match="attribute[@name]" mode="href">
		<a href="#{@href}"><code class="attribute"><xsl:value-of select="@name"/></code></a>
	</xsl:template>
	<xsl:template match="type[@name]" mode="href">
		<a href="#{@href}"><code class="type"><xsl:value-of select="@name"/></code></a>
	</xsl:template>
	<xsl:template match="type" mode="href">
		<code class="type"><xsl:value-of select="@href"/></code>
	</xsl:template>
	<xsl:template match="typeReference" mode="href">
		<xsl:variable name="target" select="$typeDefinitionList[@id = current()/@id]"/>
		<xsl:apply-templates select="$target" mode="href"/>
	</xsl:template>
	<xsl:template match="category[@name]" mode="href">
		<a href="#{@href}"><code class="category"><xsl:value-of select="@name"/></code></a>
	</xsl:template>
	<xsl:template match="foreignType[@name]" mode="href">
		<xsl:variable name="content">
			<code class="type">
				<xsl:if test="@prefix != ''">
					<xsl:value-of select="concat(@prefix, ':')"/>
				</xsl:if>
				<xsl:value-of select="@name"/>
			</code>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="@namespace = 'http://www.w3.org/2001/XMLSchema'">
				<a href="https://www.w3.org/TR/xmlschema-2/#{@name}" target="_blank" rel="external"><xsl:copy-of select="$content"/></a>
			</xsl:when>
			<xsl:otherwise>
				<a href="{@namespace}" target="_blank" rel="external"><xsl:copy-of select="$content"/></a>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="token[@name]" mode="href">
		<code class="token">"<xsl:value-of select="@name"/>"</code>
	</xsl:template>	
	<xsl:template match="pattern[@name]" mode="href">
		<code class="pattern"><xsl:value-of select="@name"/></code>
	</xsl:template>	
	<xsl:template match="childNamespace[@name]" mode="href">
		<xsl:text>Elemente aus dem Namensraum </xsl:text>
		<a href="{@name}" target="_blank" rel="external"><code class="namespace"><xsl:value-of select="@name"/></code></a>
	</xsl:template>
</xsl:stylesheet>
