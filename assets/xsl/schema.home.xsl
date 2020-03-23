<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssv="http://schema.slothsoft.net/schema/versioning">

	<xsl:include href="farah://slothsoft@schema/xsl/schema.global" />

	<xsl:template match="*" mode="body">
		<p>
			<xsl:value-of select="$info/ssv:description" />
		</p>
		<p data-dict="">home/table/caption</p>
		<table>
			<thead>
				<tr>
					<th data-dict="">home/table/version</th>
					<th data-dict="">home/table/revision</th>
					<th data-dict="">home/table/documentation</th>
					<th data-dict="">home/table/download</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="$infoList">
					<xsl:sort select="ssv:version" order="descending" data-type="number" />
					<xsl:variable name="file" select="concat(ssv:prefix, '-', ssv:revision, '.xsd')" />
					<tr>
						<td class="version">
							<xsl:value-of select="ssv:version" />
						</td>
						<td class="version">
							<xsl:value-of select="ssv:revision" />
						</td>
						<td>
							<a href="{ssv:version}">
								<xsl:apply-templates select="." mode="name" />
							</a>
						</td>
						<td>
							<a href="/{substring-after(@url, 'farah://')}" download="{$file}">
								<xsl:value-of select="$file" />
							</a>
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
</xsl:stylesheet>