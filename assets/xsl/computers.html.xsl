<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssc="http://schema.slothsoft.net/schema/computers">
    <xsl:import href="farah://slothsoft@schema/xsl/computers.functions" />

    <xsl:template name="ssc:index" match="ssc:index">
        <article class="ssc">
            <details open="open" class="ssc__toc">
                <summary>Table of Contents</summary>
                <ul>
                    <xsl:if test=".//ssc:computer">
                        <li>
                            <a href="#computers">Computers</a>
                            <ul>
                                <xsl:for-each select=".//ssc:computer">
                                    <li>
                                        <a href="#{ssc:name-to-id()}">
                                            <xsl:value-of select="@name" />
                                        </a>
                                    </li>
                                </xsl:for-each>
                            </ul>
                        </li>
                    </xsl:if>
                    <xsl:if test=".//ssc:parts">
                        <li>
                            <a href="#parts">Parts</a>
                            <ul>
                                <xsl:for-each select=".//ssc:parts">
                                    <li>
                                        <a href="#{ssc:name-to-id()}">
                                            <xsl:value-of select="@name" />
                                        </a>
                                    </li>
                                </xsl:for-each>
                            </ul>
                        </li>
                    </xsl:if>
                </ul>
            </details>
            <hr />
            <xsl:if test=".//ssc:computer">
                <article id="computers">
                    <h2>
                        <a href="#computers">Computers</a>
                    </h2>
                    <div class="ssc__list ssc__list--computers">
                        <xsl:for-each select=".//ssc:computer">
                            <article id="{ssc:name-to-id()}" class="ssc__computer">
                                <h3>
                                    <a href="#{ssc:name-to-id()}">
                                        <xsl:value-of select="@name" />
                                    </a>
                                </h3>
                                <xsl:apply-templates select="self::ssc:computer" mode="info" />
                                <xsl:apply-templates select="." mode="table" />
                            </article>
                        </xsl:for-each>
                    </div>
                </article>
            </xsl:if>
            <xsl:if test=".//ssc:parts">
                <xsl:if test=".//ssc:computer">
                    <hr />
                </xsl:if>
                <article id="parts">
                    <h2>
                        <a href="#parts">Parts</a>
                    </h2>
                    <div class="ssc__list ssc__list--parts">
                        <xsl:for-each select=".//ssc:parts">
                            <article id="{ssc:name-to-id()}" class="ssc__computer">
                                <h3>
                                    <a href="#{ssc:name-to-id()}">
                                        <xsl:value-of select="@name" />
                                    </a>
                                </h3>
                                <xsl:apply-templates select="self::ssc:computer" mode="info" />
                                <xsl:apply-templates select="." mode="table" />
                            </article>
                        </xsl:for-each>
                    </div>
                </article>
            </xsl:if>
        </article>
    </xsl:template>

    <xsl:template match="ssc:computer" mode="info">
        <xsl:variable name="parts" select="ssc:parts(.)" />

        <dl class="ssc__info">
            <xsl:for-each select="$parts//ssc:cpu">
                <dt>
                    <xsl:value-of select="../@name" />
                </dt>
                <dd>
                    <xsl:value-of select="concat(@cores, '×', @frequency)" />
                </dd>
            </xsl:for-each>
            <xsl:if test="$parts//ssc:ram">
                <dt>RAM</dt>
                <dd>
                    <xsl:value-of select="ssc:ram()" />
                </dd>
            </xsl:if>
            <xsl:for-each select="$parts//ssc:gpu">
                <dt>
                    <xsl:value-of select="../@name" />
                </dt>
                <dd>
                    <div>
                        <xsl:value-of select="ssc:format-byte(ssc:parse-byte(@memory))" />
                    </div>
                </dd>
            </xsl:for-each>
            <xsl:if test="$parts//ssc:hdd">
                <dt>Hard Disk Space</dt>
                <dd>
                    <xsl:value-of select="ssc:hdd()" />
                </dd>
            </xsl:if>
            <xsl:for-each select="$parts//ssc:display">
                <dt>
                    <xsl:value-of select="../@name" />
                </dt>
                <dd>
                    <xsl:value-of select="concat(ssc:format-x(@resolution), ' @ ', @frequency)" />
                </dd>
            </xsl:for-each>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:computer | ssc:parts" mode="table">

        <xsl:variable name="price" select="ssc:format-price(ssc:price(.))" />

        <xsl:variable name="final-price" select="ssc:format-price(ssc:final-price(.))" />

        <table class="ssc__parts">
            <thead>
                <tr>
                    <th>Part</th>
                    <th>Name</th>
                    <th>Properties</th>
                    <th>I/O</th>
                    <th>Price</th>
                </tr>
            </thead>
            <xsl:if test="self::ssc:computer">
                <tfoot>
                    <tr>
                        <td />
                        <th>Total</th>
                        <td />
                        <td />
                        <th class="ssc__price">
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
            </xsl:if>
            <tbody>
                <xsl:apply-templates select="*" mode="table" />
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="ssc:part | ssc:use-part" mode="table">
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
            <td class="ssc__price">
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
        <dl class="ssc__properties">
            <dt>Size:</dt>
            <dd>
                <xsl:value-of select="concat(@count, '×', @size)" />
            </dd>
            <dt>Type:</dt>
            <dd>
                <xsl:value-of select="@type" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:hdd" mode="name">
        HDD
    </xsl:template>

    <xsl:template match="ssc:hdd" mode="properties">
        <dl class="ssc__properties">
            <dt>Size:</dt>
            <dd>
                <xsl:value-of select="ssc:format-byte(ssc:parse-byte(@size))" />
            </dd>
            <dt>Type:</dt>
            <dd>
                <xsl:value-of select="ssc:format-x(@type)" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:chassis" mode="name">
        Chassis
    </xsl:template>

    <xsl:template match="ssc:chassis" mode="properties">
        <dl class="ssc__properties">
            <dt>Form Factor:</dt>
            <dd>
                <xsl:value-of select="@size" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:cpu" mode="name">
        CPU
    </xsl:template>

    <xsl:template match="ssc:cpu" mode="properties">
        <dl class="ssc__properties">
            <dt>Socket:</dt>
            <dd>
                <xsl:value-of select="@socket" />
            </dd>
            <dt>Frequency:</dt>
            <dd>
                <xsl:value-of select="@frequency" />
            </dd>
            <dt>Cores:</dt>
            <dd>
                <xsl:value-of select="@cores" />
            </dd>
            <dt>RAM:</dt>
            <dd>
                <xsl:value-of select="@ram" />
            </dd>
            <dt>PCIe:</dt>
            <dd>
                <xsl:value-of select="concat('v', @pcie)" />
            </dd>
            <xsl:if test="@cache-l1">
                <dt>Level 1 Cache:</dt>
                <dd>
                    <xsl:value-of select="@cache-l1" />
                </dd>
            </xsl:if>
            <xsl:if test="@cache-l2">
                <dt>Level 2 Cache:</dt>
                <dd>
                    <xsl:value-of select="@cache-l2" />
                </dd>
            </xsl:if>
            <xsl:if test="@cache-l3">
                <dt>Level 3 Cache:</dt>
                <dd>
                    <xsl:value-of select="@cache-l3" />
                </dd>
            </xsl:if>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:mainboard" mode="name">
        Mainboard
    </xsl:template>

    <xsl:template match="ssc:mainboard" mode="properties">
        <dl class="ssc__properties">
            <dt>Socket:</dt>
            <dd>
                <xsl:value-of select="@socket" />
            </dd>
            <dt>RAM:</dt>
            <dd>
                <xsl:value-of select="concat(@banks, '×', @ram)" />
            </dd>
            <dt>PCIe:</dt>
            <dd>
                <xsl:value-of select="concat('v', @pcie)" />
            </dd>
            <xsl:if test="@sata">
                <dt>SATA:</dt>
                <dd>
                    <xsl:value-of select="@sata" />
                </dd>
            </xsl:if>
            <dt>Form Factor:</dt>
            <dd>
                <xsl:value-of select="@form" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:gpu" mode="name">
        GPU
    </xsl:template>

    <xsl:template match="ssc:gpu" mode="properties">
        <dl class="ssc__properties">
            <dt>RAM:</dt>
            <dd>
                <xsl:value-of select="ssc:format-byte(ssc:parse-byte(@memory))" />
            </dd>
            <xsl:if test="@frequency">
                <dt>Frequency:</dt>
                <dd>
                    <xsl:value-of select="@frequency" />
                </dd>
            </xsl:if>
            <xsl:if test="@pcie">
                <dt>PCIe:</dt>
                <dd>
                    <xsl:value-of select="concat('v', @pcie)" />
                </dd>
            </xsl:if>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:psu" mode="name">
        PSU
    </xsl:template>

    <xsl:template match="ssc:psu" mode="properties">
        <dl class="ssc__properties">
            <dt>Power:</dt>
            <dd>
                <xsl:value-of select="@power" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:audio" mode="name">
        Audio
    </xsl:template>

    <xsl:template match="ssc:audio" mode="properties">
        <dl class="ssc__properties">
            <dt>Channels:</dt>
            <dd>
                <xsl:value-of select="@channels" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:display" mode="name">
        Display
    </xsl:template>

    <xsl:template match="ssc:display" mode="properties">
        <dl class="ssc__properties">
            <dt>Size:</dt>
            <dd>
                <xsl:value-of select="@size" />
            </dd>
            <dt>Resolution:</dt>
            <dd>
                <xsl:value-of select="ssc:format-x(@resolution)" />
            </dd>
            <dt>Frequency:</dt>
            <dd>
                <xsl:value-of select="@frequency" />
            </dd>
        </dl>
    </xsl:template>

    <xsl:template match="ssc:scanner" mode="name">
        Scanner
    </xsl:template>

    <xsl:template match="ssc:scanner" mode="properties">
        <dl class="ssc__properties">
            <dt>Scan Type:</dt>
            <dd>
                <xsl:value-of select="@scan-type" />
            </dd>
            <dt>Scan Resolution:</dt>
            <dd>
                <xsl:value-of select="@scan-resolution" />
            </dd>
        </dl>
    </xsl:template>
</xsl:stylesheet>
