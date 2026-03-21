<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssc="http://schema.slothsoft.net/schema/computers">
    <xsl:import href="farah://slothsoft@schema/xsl/computers.functions" />

    <xsl:template match="ssc:index">
        <article class="ssc">
            <h1>Hardware</h1>
            <details open="open" class="ssc_toc">
                <summary>Table of Contents:</summary>
                <ul>
                    <xsl:for-each select="ssc:computer">
                        <li>
                            <a href="#{ssc:name-to-id()}">
                                <xsl:value-of select="@name" />
                            </a>
                        </li>
                    </xsl:for-each>
                </ul>
            </details>
            <hr />
            <div class="ssc_list">
                <xsl:apply-templates select="ssc:computer" />
            </div>
        </article>
    </xsl:template>

    <xsl:template match="ssc:computer">

        <xsl:variable name="price" select="ssc:format-price(ssc:price(.))" />

        <xsl:variable name="final-price" select="ssc:format-price(ssc:final-price(.))" />

        <table id="{ssc:name-to-id()}" class="ssc_computer">
            <caption>
                <h2>
                    <a href="#{ssc:name-to-id()}">
                        <xsl:value-of select="@name" />
                    </a>
                </h2>
            </caption>
            <thead>
                <tr>
                    <th>Part</th>
                    <th>Name</th>
                    <th>Properties</th>
                    <th>I/O</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td />
                    <td />
                    <td />
                    <td />
                    <th class="ssc_price">
                        <xsl:choose>
                            <xsl:when test="$price != $final-price">
                                <s>
                                    <xsl:copy-of select="$price" />
                                </s>
                                <xsl:text> </xsl:text>
                                <xsl:copy-of select="$final-price" />
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:copy-of select="$final-price" />
                            </xsl:otherwise>
                        </xsl:choose>
                    </th>
                </tr>
            </tfoot>
            <tbody>
                <xsl:apply-templates select="*" mode="row" />
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="ssc:part | ssc:use-part" mode="row">
        <xsl:variable name="part" select="self::ssc:part | id(self::ssc:use-part/@id)" />

        <xsl:variable name="price">
            <xsl:choose>
                <xsl:when test="$part/@price and $part/@price-uri">
                    <a href="{$part/@price-uri}">
                        <xsl:value-of select="ssc:format-price($part/@price)" />
                    </a>
                </xsl:when>
                <xsl:when test="$part/@price">
                    <xsl:value-of select="ssc:format-price($part/@price)" />
                </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="final-price">
            <xsl:choose>
                <xsl:when test="@final-price">
                    <xsl:value-of select="ssc:format-price(@final-price)" />
                </xsl:when>
                <xsl:when test="$part/@final-price">
                    <xsl:value-of select="ssc:format-price($part/@final-price)" />
                </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <tr>
            <th>
                <xsl:apply-templates select="$part/*" mode="name" />
            </th>
            <td>
                <xsl:choose>
                    <xsl:when test="$part/@href">
                        <a href="{$part/@href}">
                            <xsl:value-of select="$part/@name" />
                        </a>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$part/@name" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <xsl:apply-templates select="$part/*" mode="properties" />
            </td>
            <td>
                <ul>
                    <xsl:for-each select="$part/ssc:io">
                        <li>
                            <xsl:value-of select="." />
                        </li>
                    </xsl:for-each>
                </ul>
            </td>
            <td class="ssc_price">
                <xsl:choose>
                    <xsl:when test="$price != '' and $final-price != ''">
                        <s>
                            <xsl:copy-of select="$price" />
                        </s>
                        <xsl:text> </xsl:text>
                        <xsl:copy-of select="$final-price" />
                    </xsl:when>
                    <xsl:when test="$final-price != ''">
                        <xsl:copy-of select="$final-price" />
                    </xsl:when>
                    <xsl:when test="$price != ''">
                        <xsl:copy-of select="$price" />
                    </xsl:when>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="*" mode="name">
        <xsl:value-of select="local-name()" />
    </xsl:template>

    <xsl:template match="ssc:io" mode="name" />

    <xsl:template match="ssc:io" mode="properties" />

    <xsl:template match="ssc:ram" mode="name">
        RAM
    </xsl:template>

    <xsl:template match="ssc:ram" mode="properties">
        <table>
            <tr>
                <th>Size:</th>
                <td>
                    <xsl:value-of select="@size" />
                </td>
            </tr>
            <tr>
                <th>Type:</th>
                <td>
                    <xsl:value-of select="@type" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:hdd" mode="name">
        HDD
    </xsl:template>

    <xsl:template match="ssc:hdd" mode="properties">
        <table>
            <tr>
                <th>Size:</th>
                <td>
                    <xsl:value-of select="@size" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:chassis" mode="name">
        Chassis
    </xsl:template>

    <xsl:template match="ssc:chassis" mode="properties">
        <table>
            <tr>
                <th>Size:</th>
                <td>
                    <xsl:value-of select="@size" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:cpu" mode="name">
        CPU
    </xsl:template>

    <xsl:template match="ssc:cpu" mode="properties">
        <table>
            <tr>
                <th>Frequency:</th>
                <td>
                    <xsl:value-of select="@frequency" />
                </td>
            </tr>
            <tr>
                <th>Cores:</th>
                <td>
                    <xsl:value-of select="@cores" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:mainboard" mode="name">
        Mainboard
    </xsl:template>

    <xsl:template match="ssc:mainboard" mode="properties">
        <table>
            <tr>
                <th>Socket:</th>
                <td>
                    <xsl:value-of select="@socket" />
                </td>
            </tr>
            <tr>
                <th>RAM:</th>
                <td>
                    <xsl:value-of select="@ram" />
                </td>
            </tr>
            <tr>
                <th>Form:</th>
                <td>
                    <xsl:value-of select="@form" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:gpu" mode="name">
        GPU
    </xsl:template>

    <xsl:template match="ssc:gpu" mode="properties">
        <table>
            <tr>
                <th>Frequency:</th>
                <td>
                    <xsl:value-of select="@frequency" />
                </td>
            </tr>
            <tr>
                <th>RAM:</th>
                <td>
                    <xsl:value-of select="@memory" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:psu" mode="name">
        PSU
    </xsl:template>

    <xsl:template match="ssc:psu" mode="properties">
        <table>
            <tr>
                <th>Power:</th>
                <td>
                    <xsl:value-of select="@power" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:audio" mode="name">
        Audio
    </xsl:template>

    <xsl:template match="ssc:audio" mode="properties">
        <table>
            <tr>
                <th>Channels:</th>
                <td>
                    <xsl:value-of select="@channels" />
                </td>
            </tr>
        </table>
    </xsl:template>

    <xsl:template match="ssc:scanner" mode="name">
        Scanner
    </xsl:template>

    <xsl:template match="ssc:scanner" mode="properties">
        <table>
            <tr>
                <th>Scan Type:</th>
                <td>
                    <xsl:value-of select="properties/@scan-type" />
                </td>
            </tr>
            <tr>
                <th>Scan Resolution:</th>
                <td>
                    <xsl:value-of select="properties/@scan-resolution" />
                </td>
            </tr>
        </table>
    </xsl:template>
</xsl:stylesheet>
