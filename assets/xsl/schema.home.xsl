<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:ssv="http://schema.slothsoft.net/schema/versioning">
	
	<xsl:include href="farah://slothsoft@schema/xsl/schema.global"/>
	
	<xsl:template match="*" mode="body">
		<p>
			<xsl:value-of select="$latest/ssv:description"/>
		</p>
		<p>
			The following specifications define this namespace:
		</p>
		<table>
			<thead>
				<tr>
					<th>Version</th>
					<th>Revision</th>
					<th>Documentation</th>
					<th>Download</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="$schema-info">
					<xsl:variable name="title" select="concat(ssv:name, ' v', ssv:version, ' ', ssv:revision)"/>
					<xsl:variable name="file" select="concat(ssv:prefix, '-', ssv:revision, '.xsd')"/>
					<tr>
						<td class="version"><xsl:value-of select="ssv:version"/></td>
						<td class="version"><xsl:value-of select="ssv:revision"/></td>
						<td>
							<a href="{ssv:version}"><xsl:value-of select="$title"/></a>
						</td>
						<td>
							<a href="/getAsset.php/{@xml:id}" download="{$file}"><xsl:value-of select="$file"/></a>
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
</xsl:stylesheet>