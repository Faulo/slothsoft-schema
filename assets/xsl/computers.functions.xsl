<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssc="http://schema.slothsoft.net/schema/computers" xmlns:func="http://exslt.org/functions" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="func exsl">

    <func:function name="ssc:format-price">
        <xsl:param name="price" select="." />

        <func:result select="format-number($price, '0.00&#160;€')" />
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
