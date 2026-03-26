<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssc="http://schema.slothsoft.net/schema/computers" xmlns:func="http://exslt.org/functions" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="func exsl">

    <func:function name="ssc:format-price">
        <xsl:param name="price" select="." />

        <func:result select="format-number($price, '0.00&#160;€')" />
    </func:function>

    <func:function name="ssc:format-x">
        <xsl:param name="value" select="." />

        <func:result select="translate($value, 'x', '×')" />
    </func:function>

    <func:function name="ssc:price">
        <xsl:param name="computer" select="." />

        <func:result select="sum(ssc:parts($computer)/*/@price)" />
    </func:function>

    <func:function name="ssc:final-price">
        <xsl:param name="computer" select="." />

        <xsl:choose>
            <xsl:when test="$computer/@final-price">
                <func:result select="$computer/@final-price" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:variable name="prices" select="ssc:final-price-nodes($computer)" />
                <func:result select="sum(exsl:node-set($prices)/*)" />
            </xsl:otherwise>
        </xsl:choose>
    </func:function>

    <func:function name="ssc:final-price-nodes">
        <xsl:param name="computer" select="." />

        <func:result>
            <xsl:for-each select="$computer/*">
                <price>
                    <xsl:choose>
                        <xsl:when test="@final-price">
                            <xsl:value-of select="@final-price" />
                        </xsl:when>
                        <xsl:when test="@id and id(@id)/@final-price">
                            <xsl:value-of select="id(@id)/@final-price" />
                        </xsl:when>
                        <xsl:when test="@price">
                            <xsl:value-of select="@price" />
                        </xsl:when>
                        <xsl:when test="@id and id(@id)/@price">
                            <xsl:value-of select="id(@id)/@price" />
                        </xsl:when>
                        <xsl:otherwise>
                            0
                        </xsl:otherwise>
                    </xsl:choose>
                </price>
            </xsl:for-each>
        </func:result>
    </func:function>

    <func:function name="ssc:ram">
        <xsl:param name="computer" select="." />

        <xsl:variable name="ram" select="ssc:parts($computer)/*/ssc:ram" />

        <func:result select="ssc:format-byte(sum(exsl:node-set(ssc:memory-nodes($ram))/*))" />
    </func:function>

    <func:function name="ssc:memory-nodes">
        <xsl:param name="memory" />

        <func:result>
            <xsl:for-each select="$memory">
                <memory>
                    <xsl:choose>
                        <xsl:when test="@memory">
                            <xsl:value-of select="ssc:parse-byte(@memory)" />
                        </xsl:when>
                        <xsl:when test="@count and @size">
                            <xsl:value-of select="@count * ssc:parse-byte(@size)" />
                        </xsl:when>
                        <xsl:when test="@size">
                            <xsl:value-of select="ssc:parse-byte(@size)" />
                        </xsl:when>
                    </xsl:choose>
                </memory>
            </xsl:for-each>
        </func:result>
    </func:function>

    <func:function name="ssc:hdd">
        <xsl:param name="computer" select="." />

        <xsl:variable name="hdd" select="ssc:parts($computer)/*/ssc:hdd" />

        <func:result select="ssc:format-byte(sum(exsl:node-set(ssc:memory-nodes($hdd))/*))" />
    </func:function>

    <func:function name="ssc:parse-byte">
        <xsl:param name="size" select="." />

        <xsl:variable name="number" select="substring-before($size, ' ')" />
        <xsl:variable name="unit" select="substring-after($size, ' ')" />

        <xsl:choose>
            <xsl:when test="$unit = 'B'">
                <func:result select="$number" />
            </xsl:when>
            <xsl:when test="$unit = 'KB'">
                <func:result select="$number * 1000" />
            </xsl:when>
            <xsl:when test="$unit = 'MB'">
                <func:result select="$number * 1000000" />
            </xsl:when>
            <xsl:when test="$unit = 'GB'">
                <func:result select="$number * 1000000000" />
            </xsl:when>
            <xsl:when test="$unit = 'TB'">
                <func:result select="$number * 1000000000000" />
            </xsl:when>
        </xsl:choose>
    </func:function>

    <func:function name="ssc:format-byte">
        <xsl:param name="number" select="." />

        <xsl:choose>
            <xsl:when test="$number >= 1000000000000">
                <func:result select="concat($number div 1000000000000, ' TB')" />
            </xsl:when>
            <xsl:when test="$number >= 1000000000">
                <func:result select="concat($number div 1000000000, ' GB')" />
            </xsl:when>
            <xsl:when test="$number >= 1000000">
                <func:result select="concat($number div 1000000, ' MB')" />
            </xsl:when>
            <xsl:when test="$number >= 1000">
                <func:result select="concat($number div 1000, ' KB')" />
            </xsl:when>
            <xsl:otherwise>
                <func:result select="concat($number, ' B')" />
            </xsl:otherwise>
        </xsl:choose>
    </func:function>

    <func:function name="ssc:name-to-id">
        <xsl:param name="name" select="@name" />

        <func:result select="translate($name, ' ', '_')" />
    </func:function>

    <func:function name="ssc:parts">
        <xsl:param name="computer" select="." />

        <func:result select="exsl:node-set(ssc:parts-fragment($computer))" />
    </func:function>

    <func:function name="ssc:parts-fragment">
        <xsl:param name="computer" select="." />

        <func:result>
            <xsl:for-each select="$computer | $computer/ssc:part | $computer/ssc:use-part">
                <xsl:choose>
                    <xsl:when test="self::ssc:part">
                        <xsl:copy-of select="." />
                    </xsl:when>
                    <xsl:when test="self::ssc:use-part">
                        <xsl:copy-of select="id(@id)" />
                    </xsl:when>
                </xsl:choose>
            </xsl:for-each>
        </func:result>
    </func:function>
</xsl:stylesheet>
