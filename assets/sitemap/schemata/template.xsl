<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://schema.slothsoft.net/farah/sitemap"
	xmlns:sfm="http://schema.slothsoft.net/farah/module"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/*">
		<sitemap version="1.0">
			<xsl:for-each select="*[@name='schemata']/*/*">
				<xsl:variable name="url-base" select="concat('http://schema.slothsoft.net/', @name)"/>
				<page name="{@name}" redirect=".." status-active="" status-public="">
					<xsl:for-each select="*/*">
						<xsl:variable name="url-schema" select="concat($url-base, '/', @name)"/>
						<page name="{@name}" title="{$url-schema}" ref="pages/schema/home" status-active="" status-public="">
							<sfm:param name="schema" value="{@url}"/>
							<xsl:for-each select="*/*">
								<xsl:variable name="url-version" select="concat($url-schema, '/', @name)"/>
								<page name="{@name}" title="{$url-version}" ref="pages/schema/documentation" status-active="" status-public="">
									<sfm:param name="schema" value="{@url}"/>
								</page>
							</xsl:for-each>
						</page>
					</xsl:for-each>
				</page>
			</xsl:for-each>
		</sitemap>
	</xsl:template>
</xsl:stylesheet>
				