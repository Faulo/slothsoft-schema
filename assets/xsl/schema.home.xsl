<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<xsl:import href="farah://slothsoft@farah/xsl/request" />
	
	<xsl:variable name="schema-manifest" select="document(key('request-param', 'schema'))/*"/>
	
	<xsl:template match="/*">
		<html>
			<head>
				<title>Slothsoft Schema: <xsl:value-of select="$schema-manifest/@name"/></title>
			</head>
			<body>
				<main>
					<h1>Slothsoft Schema: <xsl:value-of select="$schema-manifest/@name"/></h1>
					<table border="1">
						<thead>
							<tr>
								<th>name</th>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="$schema-manifest/*">
								<tr>
									<td><xsl:value-of select="@name"/></td>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</main>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>