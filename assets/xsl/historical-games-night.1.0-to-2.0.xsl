<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:sfs="http://schema.slothsoft.net/farah/sitemap" xmlns:sfd="http://schema.slothsoft.net/farah/dictionary"
	xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:lio="http://slothsoft.net"
	xmlns:func="http://exslt.org/functions" extension-element-prefixes="func" xmlns:ssh="http://schema.slothsoft.net/schema/historical-games-night">

	<func:function name="ssh:getDate">
		<xsl:param name="date" select="@date" />
		<xsl:choose>
			<xsl:when test="$date = ''">
				<func:result select="''" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="timestamp" select="php:function('strtotime', substring-after($date, ','))" />
				<func:result select="php:function('date', 'Y-m-d', $timestamp)" />
			</xsl:otherwise>
		</xsl:choose>
	</func:function>

	<func:function name="ssh:getTime">
		<xsl:param name="date" select="@date" />
		<xsl:choose>
			<xsl:when test="$date = ''">
				<func:result select="''" />
			</xsl:when>
			<xsl:otherwise>
				<func:result select="substring-after(substring-after($date, ' '), ' ')" />
			</xsl:otherwise>
		</xsl:choose>
	</func:function>

	<func:function name="ssh:getTrack">
		<xsl:param name="event" select="." />
		<xsl:variable name="trackId" select="substring($event/@xml:id, 1, 3)" />
		<xsl:variable name="subtrackId" select="substring($event/@xml:id, 4, 1)" />

		<func:result select="concat($trackId, '-', $subtrackId)" />
	</func:function>

	<xsl:template match="ssh:index">
		<ssh:index version="2.0">
			<xsl:for-each select="ssh:tracks">
				<ssh:tracks>
					<xsl:for-each select="ssh:track">
						<ssh:track id="{@xml:id}" name="{@name}" color="{@color}">
							<xsl:for-each select="ssh:subtrack">
								<ssh:subtrack id="{../@xml:id}-{position()}" name="{@name}" />
							</xsl:for-each>
						</ssh:track>
					</xsl:for-each>
				</ssh:tracks>
			</xsl:for-each>

			<ssh:platforms>
				<xsl:for-each select="//ssh:game/@on[not(. = preceding::ssh:game/@on)]">
					<xsl:sort select="." />
					<ssh:platform id="{.}" name="{.}" href="" />
				</xsl:for-each>
			</ssh:platforms>

			<xsl:for-each select="ssh:present">
				<ssh:present>
					<xsl:apply-templates select="ssh:event" />
				</ssh:present>
			</xsl:for-each>

			<xsl:for-each select="ssh:past">
				<ssh:past>
					<xsl:apply-templates select="ssh:event" />
				</ssh:past>
			</xsl:for-each>

			<xsl:for-each select="ssh:future">
				<ssh:future>
					<xsl:apply-templates select="ssh:event" />
				</ssh:future>
			</xsl:for-each>

			<xsl:for-each select="ssh:unfinished">
				<ssh:unfinished>
					<xsl:apply-templates select="ssh:event" />
				</ssh:unfinished>
			</xsl:for-each>

			<xsl:for-each select="ssh:unsorted">
				<ssh:unsorted>
					<xsl:apply-templates select="ssh:event" />
				</ssh:unsorted>
			</xsl:for-each>
		</ssh:index>
	</xsl:template>

	<xsl:template match="ssh:event">
		<ssh:event track="{ssh:getTrack()}">
			<xsl:copy-of select="@gfx | @moderator | @theme | @type" />
			<xsl:if test="@date">
				<xsl:attribute name="date">
                    <xsl:value-of select="ssh:getDate()" />
	            </xsl:attribute>
				<xsl:attribute name="time">
                    <xsl:value-of select="ssh:getTime()" />
	            </xsl:attribute>
			</xsl:if>
			<xsl:if test="@rerun">
				<xsl:attribute name="rerun">
                    <xsl:value-of select="ssh:getDate(id(@rerun)/@date)" />
                </xsl:attribute>
			</xsl:if>

			<xsl:copy-of select="*" />
		</ssh:event>
	</xsl:template>
</xsl:stylesheet>