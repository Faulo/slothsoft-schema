<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/schema/historical-games-night" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl"
	xmlns:func="http://exslt.org/functions" extension-element-prefixes="func" xmlns:ssh="http://schema.slothsoft.net/schema/historical-games-night">

	<func:function name="ssh:getDate">
		<xsl:param name="date" select="@date" />
		<xsl:choose>
			<xsl:when test="$date = ''">
				<func:result select="''" />
			</xsl:when>
			<xsl:when test="contains($date, ', ')">
				<xsl:variable name="timestamp" select="php:function('strtotime', substring-after($date, ', '))" />
				<func:result select="php:function('date', 'Y-m-d', $timestamp)" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="timestamp" select="php:function('strtotime', string($date))" />
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
			<xsl:when test="contains($date, ', ')">
				<func:result select="substring-after(substring-after($date, ' '), ' ')" />
			</xsl:when>
			<xsl:otherwise>
				<func:result select="substring-after($date, ' ')" />
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
		<index version="2.0">
			<xsl:for-each select="ssh:tracks">
				<tracks>
					<xsl:for-each select="ssh:track">
						<track id="{@xml:id}" name="{@name}" color="{@color}">
							<xsl:for-each select="ssh:subtrack">
								<subtrack id="{../@xml:id}-{position()}" name="{@name}" />
							</xsl:for-each>
						</track>
					</xsl:for-each>
				</tracks>
			</xsl:for-each>

			<platforms>
				<xsl:for-each select="//ssh:game/@on[not(. = preceding::ssh:game/@on)]">
					<xsl:sort select="." />
					<platform id="{.}" name="{.}" />
				</xsl:for-each>
			</platforms>

			<xsl:for-each select="ssh:present">
				<present>
					<xsl:apply-templates select="ssh:event" />
				</present>
			</xsl:for-each>

			<xsl:for-each select="ssh:past">
				<past>
					<xsl:apply-templates select="ssh:event" />
				</past>
			</xsl:for-each>

			<xsl:for-each select="ssh:future">
				<future>
					<xsl:apply-templates select="ssh:event" />
				</future>
			</xsl:for-each>

			<xsl:for-each select="ssh:unfinished">
				<unfinished>
					<xsl:apply-templates select="ssh:event" />
				</unfinished>
			</xsl:for-each>

			<xsl:for-each select="ssh:unsorted">
				<unsorted>
					<xsl:apply-templates select="ssh:event" />
				</unsorted>
			</xsl:for-each>
		</index>
	</xsl:template>

	<xsl:template match="ssh:event">
		<event track="{ssh:getTrack()}">
			<xsl:copy-of select="@gfx | @moderator | @theme | @type | @twitter" />

			<xsl:if test="@date">
				<xsl:attribute name="date">
                    <xsl:value-of select="ssh:getDate()" />
	            </xsl:attribute>
				<xsl:attribute name="time">
                    <xsl:value-of select="ssh:getTime()" />
	            </xsl:attribute>
			</xsl:if>

			<xsl:for-each select="ssh:req">
				<req theme="{id(@ref)/@theme}" />
			</xsl:for-each>

			<xsl:for-each select="ssh:game">
				<game>
					<xsl:attribute name="released">
                        <xsl:value-of select="@from" />
                    </xsl:attribute>
					<xsl:copy-of select="@name | @by | @on | @country | @href | @wanted | @manual | @version" />
				</game>
			</xsl:for-each>

			<xsl:for-each select="ssh:read">
				<read>
					<xsl:attribute name="released">
                        <xsl:value-of select="@year" />
                    </xsl:attribute>
					<xsl:attribute name="name">
                        <xsl:value-of select="@title" />
                    </xsl:attribute>
					<xsl:attribute name="by">
                        <xsl:value-of select="@author" />
                    </xsl:attribute>
					<xsl:copy-of select="@href" />
				</read>
			</xsl:for-each>
		</event>
	</xsl:template>
</xsl:stylesheet>