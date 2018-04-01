<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
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
					<ul>
						<xsl:for-each select="*[@name='schemata']/*/*/*/*">
							<li><xsl:value-of select="concat(../../@name, '/', @name)"/></li>
						</xsl:for-each>
					</ul>
				</main>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>