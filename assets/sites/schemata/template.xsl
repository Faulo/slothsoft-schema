<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://schema.slothsoft.net/farah/sitemap"
	xmlns:sfm="http://schema.slothsoft.net/farah/module"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/*">
		<sitemap version="1.0">
			<xsl:for-each select="*[@name='schemata']/*/*">
				<page name="{@name}" redirect=".." status-active="" status-public="">
					<xsl:for-each select="*/*">
						<page name="{@name}" ref="pages/schema/home" status-active="" status-public="">
							<sfm:param name="schema" value="{@url}"/>
							<xsl:for-each select="*/*">
								<page name="{@name}" ref="pages/schema/documentation" status-active="" status-public=""/>
							</xsl:for-each>
						</page>
					</xsl:for-each>
				</page>
			</xsl:for-each>
		</sitemap>
	</xsl:template>
</xsl:stylesheet>
				