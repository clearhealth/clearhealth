<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" indent="yes"/>

<xsl:variable name="lowercase" select="'abcdefghijklmnopqrstuvwxyz'"/>
<xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>

<!-- MAIN: TEMPLATE START -->
<xsl:template match="/">
<x12_al_ens_unitedhealthcare>
  <xsl:apply-templates select="data/ISA"/>
  <xsl:apply-templates select="data/GS"/>
  <!--<xsl:value-of select="current()"/>-->
  <xsl:for-each select="data/data">
    <xsl:apply-templates select="HL"/>
  </xsl:for-each>

  <!--<xsl:apply-templates select="data/footer"/>-->
  <!-- FOOTER -->

  <!-- TRANSACTION SET TRAILER -->
  <SE>SE*</SE>
  <SE01>SEGMENT_CTR*</SE01> <!-- Transaction Segment Count - N0 R1-10 -->
  <SE02>000000001~</SE02> <!-- Transaction Set Control Number - AN R4-9 -->
  <!-- FUNCTION GROUP TRAILER -->
  <GE>GE*</GE>
  <GE01>1*</GE01> <!-- Number of Transaction Sets Included - N0 R1-6 -->
  <GE02><xsl:value-of select="data/GS/claim/claimId"/>~</GE02> <!-- Group Control Number - N0 R1-9 -->
  <!-- INTERCHANGE CONTROL TRAILER -->
  <IEA>IEA*</IEA>
  <IEA01>1*</IEA01> <!-- Number of Included Functional Groups - N0 R1-5 -->
  <IEA02><xsl:call-template name="str-pad-left">
      <xsl:with-param name="input" select="data/GS/claim/claimId"/>
      <xsl:with-param name="string" select="'0'"/>
      <xsl:with-param name="length" select="'9'"/>
    </xsl:call-template>~</IEA02> <!-- Interchange Control Number - N0 R9 -->
</x12_al_ens_unitedhealthcare>
</xsl:template>
<!-- MAIN: TEMPLATE END -->


<!-- TEMPLATE: ISA START -->
<xsl:template match="data/ISA">
  <!-- INTERCHANGE CONTROL HEADER -->
  <ISA>ISA*</ISA>
  <ISA01>00*</ISA01> <!-- Authorization Information Qualifier - ID R2 options: 00,03 -->
  <ISA02><xsl:call-template name="str-pad-right">
      <xsl:with-param name="input" select="''"/>
      <xsl:with-param name="length" select="'10'"/>
    </xsl:call-template>*</ISA02> <!-- Authorization Information - AN R10 -->
  <ISA03>00*</ISA03> <!-- Security Information Qualifier ID R2 options: 00,01 -->
  <ISA04><xsl:call-template name="str-pad-right">
      <xsl:with-param name="input" select="''"/>
      <xsl:with-param name="length" select="'10'"/>
    </xsl:call-template>*</ISA04> <!-- Security Information - AN R10 -->
  <ISA05>ZZ*</ISA05> <!-- Interchange ID Qualifier - ID R2 options: 01, 14, 20, 27, 28, 29, 30, 33, ZZ -->
  <ISA06><xsl:variable name="edi7var">
      <xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="practice/senderId"/>
        <xsl:with-param name="length" select="'15'"/>
      </xsl:call-template>
    </xsl:variable>
    <xsl:value-of select="translate($edi7var, $lowercase, $uppercase)"/>*</ISA06> <!-- Interchange Sender ID - AN R15 -->
  <ISA07>ZZ*</ISA07> <!-- Interchange ID Qualifier - ID R2 options: 01, 14, 20, 27, 28, 29, 30, 33, ZZ -->
  <ISA08><xsl:call-template name="str-pad-right">
      <xsl:with-param name="input" select="'87726'"/>
      <xsl:with-param name="length" select="'15'"/>
    </xsl:call-template>*</ISA08> <!-- Interchange Receiver ID - AN R15 -->
  <ISA09><xsl:value-of select="dateNow"/>*</ISA09> <!-- Interchange Date - DT R6 format: YYMMDD -->
  <ISA10><xsl:value-of select="timeNow"/>*</ISA10> <!-- Interchange Time - TM R4 format: HHMM -->
  <ISA11>U*</ISA11> <!-- Interchange Control Standards ID - ID R1 value: U -->
  <ISA12>00401*</ISA12> <!-- Interchange Control Version Number - ID R5 value: 00401 -->
  <ISA13><xsl:call-template name="str-pad-left">
      <xsl:with-param name="input" select="claim/claimId"/>
      <xsl:with-param name="string" select="'0'"/>
      <xsl:with-param name="length" select="'9'"/>
    </xsl:call-template>*</ISA13> <!-- Interchange Control Number - N0 R9 -->
  <ISA14>0*</ISA14> <!-- Acknowledgement Requested - ID R1 options: 0,1 -->
  <ISA15><xsl:choose>
      <xsl:when test="testing">T</xsl:when>
      <xsl:otherwise>P</xsl:otherwise>
    </xsl:choose>*</ISA15> <!-- Usage Indicator - ID R1 options: P,T -->
  <ISA16>:~</ISA16> <!-- Component Element Separator - AN R1 -->
</xsl:template>
<!-- TEMPLATE: ISA END -->

<!-- TEMPLATE: GS START -->
<xsl:template match="data/GS">
  <!-- FUNCTIONAL GROUP HEADER -->
  <GS>GS*</GS>
  <GS01>HC*</GS01> <!-- Functional Identifier Code - ID R2 value: HC -->
  <GS02><xsl:value-of select="translate(practice/senderId, $lowercase, $uppercase)"/>*</GS02> <!-- Application Sender Code - AN R2-15 -->
  <GS03>87726*</GS03> <!-- Application Receiver Code - AN R2-15 -->
  <GS04><xsl:value-of select="dateNow"/>*</GS04> <!-- Date - DT R8 format: CCYYMMDD -->
  <GS05><xsl:value-of select="timeNow"/>*</GS05> <!-- Time - TM R4-8 format: HHMMSSDD -->
  <GS06><xsl:value-of select="claim/claimId"/>*</GS06> <!-- Group Control Number - N0 R1-9 -->
  <GS07>X*</GS07> <!-- Responsible Agency Code - ID R1-2 value: X -->
  <GS08>004010X098A1~</GS08> <!-- Version Identifier Code - AN R1-12 value: 004010X098A1 -->

  <!-- TRANSACTION SET HEADER -->
  <ST>ST*</ST>
  <ST01>837*</ST01> <!-- Transaction Set Identifier Code - ID R3 value: 837 -->
  <ST02>000000001~</ST02> <!-- Transaction Set Control Number - AN R4-9 -->

  <!-- BEGINNING OF HIERARCHICAL TRANSACTION -->
  <BHT>BHT*</BHT>
  <BHT01>0019*</BHT01> <!-- Hierarchical Structure Code - ID R4 value: 0019 -->
  <BHT02>00*</BHT02> <!-- Transaction Set Purpose Code - ID R2 options: 00, 18 -->
  <BHT03>000508*</BHT03> <!-- Originator Application Transaction ID - AN R1-30 -->
  <BHT04><xsl:value-of select="dateNow"/>*</BHT04> <!-- Transaction Set Creation Date - DT R8 format: CCYYMMDD -->
  <BHT05><xsl:value-of select="timeNow"/>*</BHT05> <!-- Transaction Set Creation Time - TM R4-8 format: HHMM, HHMMSS, HHMMSSD, HHMMSSDD -->
  <BHT06>CH~</BHT06> <!-- Claim or Encounter ID - ID R2 options: CH, RP -->

  <!-- TRANSMISSION TYPE IDENTIFICATION -->
  <REF>REF*</REF>
  <REF01>87*</REF01> <!-- Reference Identification Qualifier - ID R2-3 value: 87 -->
  <REF02>004010X098A1~</REF02> <!-- Transmission Type Code - AN R1-30 options: 004010X098A1, 004010X098DA1 -->
  <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
  <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->

  <!-- SUBMITTER NAME - 1000A -->
  <NM1>NM1*</NM1>
  <NM101>41*</NM101> <!-- Entity Identifier Code - ID R2-3 value: 41 -->
  <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1,2 -->
  <NM103><xsl:value-of select="translate(practice/name, $lowercase, $uppercase)"/>*</NM103> <!-- Submitter Last or Organization Name - AN R1-35 -->
  <NM104>*</NM104> <!-- Submitter First Name - AN S1-25 -->
  <NM105>*</NM105> <!-- Submitter Middle Name - AN S1-25 -->
  <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
  <NM107>*</NM107> <!-- Name Suffix - AN N/U1-10 -->
  <NM108>46*</NM108> <!-- Identification Code Qualifier - ID R1-2 value: 46 -->
  <NM109><xsl:value-of select="translate(practice/senderId, $lowercase, $uppercase)"/>~</NM109> <!-- Submitter Identifier - AN R2-80 -->
  <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
  <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

  <!-- SUBMITTER EDI CONTACT INFORMATION -->
  <PER>PER*</PER>
  <PER01>IC*</PER01> <!-- Contact Function Code - ID R2 value: IC -->
  <PER02><xsl:value-of select="translate(practice/name, $lowercase, $uppercase)"/>*</PER02> <!-- Submitter Contact Name - AN R1-60 -->
  <PER03>TE*</PER03> <!-- Communication Number Qualifier - ID R2 options: ED, EM, FX. TE -->
  <PER04><xsl:value-of select="practice/phoneNumber"/>~</PER04> <!-- Communication Number - AN R1-80 -->
  <!--<PER05></PER05>--> <!-- Communication Number Qualifier - ID S2 options: ED, EM, EX, FX, TE -->
  <!--<PER06></PER06>--> <!-- Communication Number - AN S1-80 -->
  <!--<PER07></PER07>--> <!-- Communication Number Qualifier - ID S2 options: ED, EM, EX, FX, TE -->
  <!--<PER08></PER08>--> <!-- Communication Number - AN S1-80 -->
  <!--<PER09></PER09>--> <!-- Contact Inquiry Reference - AN N/U1-20 -->

  <!-- RECEIVER NAME - 1000B -->
  <NM1>NM1*</NM1>
  <NM101>40*</NM101> <!-- Entity Identifier Code - ID R2-3 value: 40 -->
  <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 value: 2 -->
  <NM103>UNITED HEALTHCARE*</NM103> <!-- Receiver Name - AN R1-35 -->
  <NM104>*</NM104> <!-- Name First - AN S1-25 -->
  <NM105>*</NM105> <!-- Name Middle - AN S1-25 -->
  <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
  <NM107>*</NM107> <!-- Name Suffix - AN N/U1-10 -->
  <NM108>46*</NM108> <!-- Identification Code Qualifier - ID R1-2 value: 46 -->
  <NM109>87726~</NM109> <!-- Receiver Primary Identifier - AN R2-80 -->
  <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
  <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->
</xsl:template>
<!-- TEMPLATE: GS END -->


<!-- TEMPLATE: HL START -->
<xsl:template match="HL">
  <!-- BILLING/PAY-TO PROVIDER HIERARCHICAL LEVEL - 2000A -->
  <HL>HL*</HL>
  <HL01><xsl:value-of select="hlCount" />*</HL01> <!-- Hierarchical ID Number - AN R1-12 -->
  <HL02>*</HL02> <!-- Hierarchical Parent ID Number - AN N/U1-12 -->
  <HL03>20*</HL03> <!-- Hierarchical Level Code - ID R1-2 value: 20 -->
  <HL04>1~</HL04> <!-- Hierarchical Child Code - ID R1 value: 1 -->

  <!-- NOTES:
  PRV - BILLING/PAY-TO PROVIDER SPECIALTY INFORMATION 2000A - optional
  CUR - FOREIGN CURRENCY INFORMATION -2000A optional
  -->

  <!-- Billing Provider Name Suffix - 2010AA -->
  <NM1>NM1*</NM1>
  <NM101>85*</NM101> <!-- Entity Identifier Code - ID R2-3 value: 85 -->
  <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1,2 -->
  <NM103><xsl:value-of select="translate(practice/name, $lowercase, $uppercase)"/>*</NM103> <!-- Billing Provider Last or Organizational Name - AN R1-35 -->
  <NM104>*</NM104> <!-- Billing Provider First Name - AN S1-25 -->
  <NM105>*</NM105> <!-- Billing Provider Middle Name - AN S1-25 -->
  <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
  <NM107>*</NM107> <!-- Billing Provider Name Suffix - AN S1-10 -->
  <NM108><xsl:value-of select="practice/identifier_type"/>*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: 24, 34, XX -->
  <NM109><xsl:value-of select="translate(practice/identifier, $lowercase, $uppercase)"/>~</NM109> <!-- Billing Provider Identifier - AN R2-80 -->
  <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
  <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

  <!-- BILLING PROVIDER ADDRESS - 2010AA -->
  <N3>N3*</N3>
  <N301><xsl:value-of select="translate(practice/address/line1, $lowercase, $uppercase)"/></N301> <!-- Billing Provider Address Line - AN R1-55 -->
  <N302><xsl:if test="practice/address/line2 != ''">*<xsl:value-of select="translate(practice/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Billing Provider Address Line - AN S1-55 -->

  <!-- BILLING PROVIDER CITY/STATE/ZIP CODE - 2010AA -->
  <N4>N4*</N4>
  <N401><xsl:value-of select="translate(practice/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Billing Provider City Name - AN R2-30 -->
  <N402><xsl:value-of select="translate(practice/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Billing Provider State or Province Code - ID R2 -->
  <N403><xsl:call-template name="str-pad-right">
      <xsl:with-param name="input" select="practice/address/zip"/>
      <xsl:with-param name="length" select="'5'"/>
    </xsl:call-template>~</N403> <!-- Billing Provider Postal Zone or ZIP Code - ID R3-15 -->
  <!--<N404></N404>--> <!-- Country Code - ID S2-3 -->
  <!--<N405></N405>--> <!-- Location Qualifier - ID N/U1-2 -->
  <!--<N406></N406>--> <!-- Location Identifier - AN N/U1-30 -->

  <!-- REF - BILLING PROVIDER SECONDARY IDENTIFICATION - 2010AA optional -->
  <REF>REF*</REF>
  <!--<REF01>G2*</REF01>--> <!-- Reference Identification Qualifier - ID R2-3 options: 0B, 1A, 1B, 1C, 1D, 1G, 1H, 1J, B3, BQ, EI, FH, G2, G5, LU, SY, U3, X5 -->
  <REF01>EI*</REF01>
  <REF02><xsl:value-of select="translate(treating_facility/identifier, $lowercase, $uppercase)"/>~</REF02> <!-- Pay-to Provider Identifier - AN R1-30 -->
  <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
  <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->

  <!-- NOTES:
  REF - CREDIT/DEBIT CARD BILLING INFORMATION - 2010AA optional
  -->

  <!-- BILLING PROVIDER CONTACT INFORMATION - 2010AA optional -->
  <PER>PER*</PER>
  <PER01>IC*</PER01> <!-- Contact Function Code - ID R2 value: IC -->
  <PER02><xsl:value-of select="translate(practice/name, $lowercase, $uppercase)"/>*</PER02> <!-- Billing Provider Contact Name - AN R1-60 -->
  <PER03>TE*</PER03> <!-- Communication Number Qualifier - ID R2 options: EM, FX, TE -->
  <PER04><xsl:value-of select="practice/phoneNumber"/>~</PER04> <!-- Communication Number - AN R1-80 -->
  <!--<PER05></PER05>--> <!-- Communication Number Qualifier - ID S2 options: EM, EX, FX, TE -->
  <!--<PER06></PER06>--> <!-- Communication Number - AN S1-80 -->
  <!--<PER07></PER07>--> <!-- Communication Number Qualifier - ID S2 options: EM, EX, FX, TE -->
  <!--<PER08></PER08>--> <!-- Communication Number - AN S1-80 -->
  <!--<PER09></PER09>--> <!-- Contact Inquiry Reference - AN N/U1-20 -->

  <!-- PAY-TO PROVIDER NAME - 2010AB optional -->
  <NM1>NM1*</NM1>
  <NM101>87*</NM101> <!-- Entity Identifier Code - ID R2-3 value: 87 -->
  <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1,2 -->
  <NM103><xsl:value-of select="translate(practice/name, $lowercase, $uppercase)"/>*</NM103> <!-- Pay-to Provider Last or Organization Name - AN R1-35 -->
  <NM104>*</NM104> <!-- Pay-to Provider First Name - AN S1-25 -->
  <NM105>*</NM105> <!-- Pay-to Provider Middle Name - AN S1-25 -->
  <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
  <NM107>*</NM107> <!-- Pay-to Provider Name Suffix - AN S1-10 -->
  <NM108><xsl:value-of select="practice/identifier_type"/>*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: 24, 34, XX -->
  <NM109><xsl:value-of select="translate(practice/identifier, $lowercase, $uppercase)"/>~</NM109> <!-- Pay-to Provider Identifier - AN R2-80 -->
  <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
  <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

  <!-- PAY-TO PROVIDER ADDRESS - 2010AB -->
  <N3>N3*</N3>
  <N301><xsl:value-of select="translate(practice/address/line1, $lowercase, $uppercase)"/></N301> <!-- Pay-to Provider Address Line - AN R1-55 -->
  <N302><xsl:if test="practice/address/line2 != ''">*<xsl:value-of select="translate(practice/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Pay-to Provider Address Line - AN S1-55 -->

  <!-- PAY-TO PROVIDER CITY/STATE/ZIP CODE - 2010AB -->
  <N4>N4*</N4>
  <N401><xsl:value-of select="translate(practice/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Pay-to Provider City Name - AN R2-30 -->
  <N402><xsl:value-of select="translate(practice/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Pay-to Provider State or Province Code - ID R2 -->
  <N403><xsl:call-template name="str-pad-right">
      <xsl:with-param name="input" select="practice/address/zip"/>
      <xsl:with-param name="length" select="'5'"/>
    </xsl:call-template>~</N403> <!-- Pay-to Provider Postal Zone or ZIP Code - ID R3-15 -->
  <!--<N404></N404>--> <!-- Pay-to Provider Country Code - ID S2-3 -->
  <!--<N405></N405>--> <!-- Location Qualifier - ID N/U1-2 -->
  <!--<N406></N406>--> <!-- Location Identifier - AN N/U1-30 -->

  <!-- PAY-TO PROVIDER SECONDARY IDENTIFICATION - 2010AB -->
  <REF>REF*</REF>
  <!--<REF01>G2*</REF01>--> <!-- Reference Identification Qualifier - ID R2-3 options: 0B, 1A, 1B, 1C, 1D, 1G, 1H, 1J, B3, BQ, EI, FH, G2, G5, LU, SY, U3, X5 -->
  <REF01>EI*</REF01>
  <REF02><xsl:value-of select="translate(treating_facility/identifier, $lowercase, $uppercase)"/>~</REF02> <!-- Pay-to Provider Identifier - AN R1-30 -->
  <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
  <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->

  <xsl:for-each select="HL2">
    <!-- SUBSCRIBER HIERARCHICAL LEVEL - 2000B -->
    <HL>HL*</HL>
    <HL01><xsl:value-of select="hlCount2"/>*</HL01> <!-- Hierarchical ID Number - AN R1-12 -->
    <HL02><xsl:value-of select="hlCount"/>*</HL02> <!-- Hierarchical Parent ID Number - AN R1-12  -->
    <HL03>22*</HL03> <!-- Hierarchical Level Code - ID R1-2 value: 22 -->
    <HL04>0~</HL04> <!-- Hierarchical Child Code - ID R1-1 options: 0, 1 -->

    <!-- SUBSCRIBER INFORMATION - 2000B -->
    <SBR>SBR*</SBR>
    <SBR01><xsl:value-of select="translate(payer/responsibility, $lowercase, $uppercase)"/>*</SBR01> <!-- Payer Responsibility Sequence Number Code - ID R1 options: P, S, T -->
    <SBR02><xsl:value-of select="translate(subscriber/relationship_code, $lowercase, $uppercase)"/>*</SBR02> <!-- Individual Relationship Code - ID S2 value: 18 -->
    <SBR03><xsl:value-of select="translate(subscriber/group_number, $lowercase, $uppercase)"/>*</SBR03> <!-- Insured Group or Policy Number - AN S1-30 -->
    <SBR04><xsl:value-of select="translate(subscriber/group_name, $lowercase, $uppercase)"/>*</SBR04> <!-- Insured Group Name - AN S1-60 -->
    <SBR05>*</SBR05> <!-- Insurance Type Code - ID S1-3 options: 12, 13, 14, 15, 16, 41, 42, 43, 47 -->
    <SBR06>*</SBR06> <!-- Coordination of Benefits Code - ID N/U1 -->
    <SBR07>*</SBR07> <!-- Yes/No Condition or Response Code - ID N/U1 -->
    <SBR08>*</SBR08> <!-- Employment Status Code - ID N/U2 -->
    <SBR09>MB~</SBR09> <!-- Claim Filing Indicator Code - ID S1-2 options: 09, 10, 11, 12, 13, 14, 15, 16, AM, BL, CH, CI, DS, HM, LI, LM, MB, MC, OF, TV, VA, WC, ZZ -->

    <!--<xsl:if test="subscriber/relationship = 'self'">-->
    <xsl:if test="patient/date_of_death != ''">
    <!-- PATIENT INFORMATION - 2000B optional -->
    <PAT>PAT*</PAT>
    <PAT01>*</PAT01> <!-- Individual Relationship Code - ID N/U2 -->
    <PAT02>*</PAT02> <!-- Patient Location Code - ID N/U1 -->
    <PAT03>*</PAT03> <!-- Employment Status Code - ID N/U2 -->
    <PAT04>*</PAT04> <!-- Student Status Code - ID N/U1 -->
    <PAT05>D8*</PAT05> <!-- Date Time Period Format Qualifier - ID S2-3 value: D8 -->
    <PAT06><xsl:value-of select="patient/date_of_death"/>*</PAT06> <!-- Insured Individual Death Date - AN S1-35 format: CCYYMMDD -->
    <PAT07>01*</PAT07> <!-- Unit or Basis for Measurement Code - ID S2 value: 01 -->
    <PAT08><xsl:value-of select="patient/weight"/>*</PAT08> <!-- Patient Weight 9(6)V99 - R S1-10 -->
    <PAT09>Y~</PAT09> <!-- Pregnancy Indicator - ID S1 value: Y -->
    </xsl:if>

    <!-- SUBSCRIBER NAME - 2010BA -->
    <NM1>NM1*</NM1>
    <NM101>IL*</NM101> <!-- Entity Identifier Code - ID R2-3 value: IL -->
    <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1,2 -->
    <NM103><xsl:value-of select="translate(subscriber/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Subscriber Last Name - AN R1-35 -->
    <NM104><xsl:value-of select="translate(subscriber/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Subscriber First Name - AN S1-25 -->
    <NM105><xsl:value-of select="translate(subscriber/middle_name, $lowercase, $uppercase)"/>*</NM105> <!-- Subscriber Middle Name - AN S1-25 -->
    <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
    <NM107>*</NM107> <!-- Subscriber Name Suffix - AN S1-10 -->
    <NM108>MI*</NM108> <!-- Identification Code Qualifier - ID S1-2 options: MI, ZZ -->
    <NM109><xsl:value-of select="subscriber/id"/>~</NM109> <!-- Subscriber Primary Identifier - AN S2-80 -->
    <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
    <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

    <!-- SUBSCRIBER ADDRESS - 2010BA optional -->
    <N3>N3*</N3>
    <N301><xsl:value-of select="translate(subscriber/address/line1, $lowercase, $uppercase)"/></N301> <!-- Subscriber Address Line - AN R1-55 -->
    <N302><xsl:if test="subscriber/address/line2 != ''">*<xsl:value-of select="translate(subscriber/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Subscriber Address Line - AN S1-55 -->

    <!-- SUBSCRIBER CITY/STATE/ZIP CODE - 2010BA optional -->
    <N4>N4*</N4>
    <N401><xsl:value-of select="translate(subscriber/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Subscriber City Name - AN R2-30 -->
    <N402><xsl:value-of select="translate(subscriber/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Subscriber State Code - ID R2 -->
    <N403><xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="subscriber/address/zip"/>
        <xsl:with-param name="length" select="'5'"/>
      </xsl:call-template>~</N403> <!-- Subscriber Postal Zone or ZIP Code - ID R3-15 -->
    <!--<N404></N404>--> <!-- Subscriber Country Code - ID S2-3 -->
    <!--<N405></N405>--> <!-- Location Qualifier - ID N/U1-2 -->
    <!--<N406></N406>--> <!-- Location Identifier - AN N/U1-30 -->

    <!-- SUBSCRIBER DEMOGRAPHIC INFORMATION - 2010BA optional -->
    <DMG>DMG*</DMG>
    <DMG01>D8*</DMG01> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
    <DMG02><xsl:value-of select="subscriber/date_of_birth"/>*</DMG02> <!-- Subscriber Birth Date - AN R1-35 format: CCYYMMDD -->
    <DMG03><xsl:value-of select="translate(subscriber/gender, $lowercase, $uppercase)"/>~</DMG03> <!-- Subscriber Gender Code - ID R1 options: F, M, U -->
    <!--<DMG04></DMG04>--> <!-- Marital Status Code - ID N/U1 -->
    <!--<DMG05></DMG05>--> <!-- Race or Ethnicity Code - ID N/U1 -->
    <!--<DMG06></DMG06>--> <!-- Citizenship Status Code - ID N/U1-2 -->
    <!--<DMG07></DMG07>--> <!-- Country Code - ID N/U2-3 -->
    <!--<DMG08></DMG08>--> <!-- Basis of Verification Code - ID N/U1-2 -->
    <!--<DMG09></DMG09>--> <!-- Quantity - R N/U1-15 -->

    <!-- NOTES:
    REF - SUBSCRIBER SECONDARY IDENTIFICATION - 2010BA optional
    REF - PROPERTY AND CASUALTY CLAIM NUMBER - 2010BA optional
    -->

    <!-- PAYER NAME - 2010BB -->
    <NM1>NM1*</NM1>
    <NM101>PR*</NM101> <!-- Entity Identifier Code - ID R2-3 value: PR -->
    <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 value: 2 -->
    <NM103>UNITED HEALTHCARE*</NM103> <!-- Payer Name - AN R1-35 -->
    <NM104>*</NM104> <!-- Name First - AN N/U1-25 -->
    <NM105>*</NM105> <!-- Name Middle - AN N/U1-25 -->
    <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
    <NM107>*</NM107> <!-- Name Suffix - AN N/U1-10 -->
    <NM108>PI*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: PI, XV -->
    <NM109>87726~</NM109> <!-- Payer Identifier - AN R2-80 -->
    <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
    <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

    <!-- NOTES:
    N3 - PAYER ADDRESS - 2010BB optional
    N4 - PAYER CITY/STATE/ZIP CODE - 2010BB optional
    REF - PAYER SECONDARY IDENTIFICATION - 2010BB optional
    -->

    <xsl:if test="responsible_party/last_name != '' and (responsible_party/last_name != patient/last_name or responsible_party/first_name != patient/first_name)">
    <!-- RESPONSIBLE PARTY NAME - 2010BC -->
    <NM1>NM1*</NM1>
    <NM101>QD*</NM101> <!-- Entity Identifier Code - ID R2-3 value: QD -->
    <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 value: 2 -->
    <NM103><xsl:value-of select="translate(responsible_party/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Responsible Party Last or Organization Name - AN R1-35 -->
    <NM104><xsl:value-of select="translate(responsible_party/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Responsible Party First Name - AN S1-25 -->
    <NM105><xsl:value-of select="translate(responsible_party/middle_name, $lowercase, $uppercase)"/>~</NM105> <!-- Responsible Party Middle Name - AN S1-25 -->
    <!--<NM106></NM106>--> <!-- Name Prefix - AN N/U1-10 -->
    <!--<NM107></NM107>--> <!-- Responsible Party Suffix Name - AN S1-10 -->
    <!--<NM108></NM108>--> <!-- Identification Code Qualifier - ID N/U1-2 -->
    <!--<NM109></NM109>--> <!-- Identification Code - AN N/U2-80 -->
    <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
    <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

    <!-- RESPONSIBLE PARTY ADDRESS - 2010BC -->
    <N3>N3*</N3>
    <N301><xsl:value-of select="translate(responsible_party/address/line1, $lowercase, $uppercase)"/></N301> <!-- Responsible Party Address Line - AN R1-55 -->
    <N302><xsl:if test="responsible_party/address/line2 != ''">*<xsl:value-of select="translate(responsible_party/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Responsible Party Address Line - AN S1-55 -->

    <!-- RESPONSIBLE PARTY CITY/STATE/ZIP CODE - 2010BC -->
    <N4>N4*</N4>
    <N401><xsl:value-of select="translate(responsible_party/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Responsible Party City Name - AN R2-30 -->
    <N402><xsl:value-of select="translate(responsible_party/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Responsible Party State Code - ID R2 -->
    <N403><xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="responsible_party/address/zip"/>
        <xsl:with-param name="length" select="'5'"/>
      </xsl:call-template>~</N403> <!-- Responsible Party Postal Zone or ZIP Code - ID R3-15 -->
    <!--<N404></N404>--> <!-- Responsible Party Country Code - ID S2-3 -->
    <!--<N405></N405>--> <!-- Location Qualifier - ID N/U1-2 -->
    <!--<N406></N406>--> <!-- Location Identifier - AN N/U1-30 -->
    </xsl:if>

    <!-- NOTES:
    NM1 - CREDIT/DEBIT CARD HOLDER NAME - 2010BD optional
    REF - CREDIT/DEBIT CARD INFORMATION - 2010BD optional

    HL - PATIENT HIERARCHICAL LEVEL - 2000C optional
    PAT - PATIENT INFORMATION - 2000C
    NM1 - PATIENT NAME - 2010CA
    N3 - PATIENT ADDRESS - 2010CA
    N4 - PATIENT CITY/STATE/ZIP CODE - 2010CA
    DMG - PATIENT DEMOGRAPHIC INFORMATION - 2010CA
    REF - PATIENT SECONDARY IDENTIFICATION - 2010CA optional
    REF - PROPERTY AND CASUALTY CLAIM NUMBER - 2010CA optional
    -->

    <xsl:for-each select="CLM">
      <!-- CLAIM INFORMATION - 2300 -->
      <CLM>CLM*</CLM>
      <CLM01><xsl:value-of select="translate(claim/claimId, $lowercase, $uppercase)"/>*</CLM01> <!-- Patient Account Number - AN R1-38 -->
      <CLM02><xsl:value-of select="claim_line/amount"/>*</CLM02> <!-- Total Claim Charge Amount S9(7)V99 - R R1-18 -->
      <CLM03>*</CLM03> <!-- Claim Filing Indicator Code - ID N/U1-2 -->
      <CLM04>*</CLM04> <!-- Non-Institutional Claim Type Code - ID N/U1-2 -->
      <CLM05></CLM05> <!-- HEALTH CARE SERVICE LOCATION INFORMATION R -->
      <CLM05-1><xsl:value-of select="translate(treating_facility/facility_code, $lowercase, $uppercase)"/>:</CLM05-1> <!-- Facility Type Code - AN R1-2 options: 11, 12, 21, 22, 23, 24, 25, 26, 31, 32, 33, 34, 41, 42, 51, 52, 53, 54, 55, 56, 50, 60, 61, 62, 65, 71, 72, 81, 99 -->
      <CLM05-2>:</CLM05-2> <!-- Facility Code Qualifier - ID N/U1-2 -->
      <CLM05-3>1*</CLM05-3> <!-- Claim Frequency Code - ID R1 value: Refer to Code Source 235 -->
      <CLM06><xsl:value-of select="translate(provider/signature_on_file, $lowercase, $uppercase)"/>*</CLM06> <!-- Provider or Supplier Signature Indicator - ID R1 options: N, Y -->
      <CLM07><xsl:value-of select="translate(provider/accepts_assignment, $lowercase, $uppercase)"/>*</CLM07> <!-- Medicare Assignment Code - ID R1 options: A, B, C, P -->
      <CLM08>Y*</CLM08> <!-- Benefits Assignment Certification Indicator - ID R1 options: N, Y -->
      <CLM09>Y*</CLM09> <!-- Release of Information Code - ID R1 options: A, I, M, N, O, Y -->
      <CLM010>C~</CLM010> <!-- Patient Signature Source Code - ID S1 options: B, C, M, P, S -->
      <!--<CLM011></CLM011>--> <!-- RELATED CAUSES INFORMATION S -->
      <!--<CLM011-1></CLM011-1>--> <!-- Related Causes Code - ID R2-3 options: AA, AP, EM, OA -->
      <!--<CLM011-2></CLM011-2>--> <!-- Related Causes Code - ID S2-3 options: AA, AP, EM, OA -->
      <!--<CLM011-3></CLM011-3>--> <!-- Related Causes Code - ID S2-3 options: AA, AP, EM, OA -->
      <!--<CLM011-4></CLM011-4>--> <!-- Auto Accident State or Province Code - ID S2 -->
      <!--<CLM011-5></CLM011-5>--> <!-- Country Code - ID S2-3 -->
      <!--<CLM012></CLM012>--> <!-- Special Program Indicator - ID S2-3 options: 01, 02, 03, 05, 07, 08, 09 -->
      <!--<CLM013></CLM013>--> <!-- Yes/No Condition or Response Code - ID N/U1 -->
      <!--<CLM014></CLM014>--> <!-- Level of Service Code - ID N/U1-3 -->
      <!--<CLM015></CLM015>--> <!-- Yes/No Condition or Response Code - ID N/U1 -->
      <!--<CLM016></CLM016>--> <!-- Participation Agreement - ID S1 value: P -->
      <!--<CLM017></CLM017>--> <!-- Claim Status Code - ID N/U1-2 -->
      <!--<CLM018></CLM018>--> <!-- Yes/No Condition or Response Code - ID N/U1 -->
      <!--<CLM019></CLM019>--> <!-- Claim Submission Reason Code - ID N/U2 -->
      <!--<CLM020></CLM020>--> <!-- Delay Reason Code - ID S1-2 options: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 -->

      <!-- DATE - INITIAL TREATMENT - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>454*</DTP01> <!-- Date Time Qualifier - ID R3 value: 454 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_initial_treatment"/>~</DTP03> <!-- Initial Treatment Date - AN R1-35 format: CCYYMMDD -->

      <!-- X12 Optional Dates Start -->
      <xsl:if test="patient/date_of_last_visit != ''">
      <!-- DATE - DATE LAST SEEN - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>304*</DTP01> <!-- Date Time Qualifier - ID R3 value: 304 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_last_visit"/>~</DTP03> <!-- Last Seen Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_onset != ''">
      <!-- DATE - ONSET OF CURRENT ILLNESS/SYMPTOM - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>431*</DTP01> <!-- Date Time Qualifier - ID R3 value: 431 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_onset"/>~</DTP03> <!-- Onset of Current Illness or Injury Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_last_visit != ''">
      <!-- DATE - ACUTE MANIFESTATION - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>453*</DTP01> <!-- Date Time Qualifier - ID R3 value: 453 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_acute_manifestation"/>~</DTP03> <!-- Acute Manifestation Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_last_visit != ''">
      <!-- DATE - SIMILAR ILLNESS/SYMPTOM ONSET - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>438*</DTP01> <!-- Date Time Qualifier - ID R3 value: 438 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_similar_onset"/>~</DTP03> <!-- Similar Illness or Symptom Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_accident != ''">
      <!-- DATE - ACCIDENT - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>439*</DTP01> <!-- Date Time Qualifier - ID R3 value: 439 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_accident"/>~</DTP03> <!-- Accident Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_last_menstrual_period != ''">
      <!-- DATE - LAST MENSTRUAL PERIOD - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>484*</DTP01> <!-- Date Time Qualifier - ID R3 value: 484 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_last_menstrual_period"/>~</DTP03> <!-- Last Menstrual Period Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_last_xray != ''">
      <!-- DATE - LAST X-RAY - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>455*</DTP01> <!-- Date Time Qualifier - ID R3 value: 455 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_last_xray"/>~</DTP03> <!-- Last X-Ray Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_hearing_vision_prescription != ''">
      <!-- DATE - HEARING AND VISION PRESCRIPTION DATE - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>471*</DTP01> <!-- Date Time Qualifier - ID R3 value: 471 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_hearing_vision_prescription"/>~</DTP03> <!-- Prescription Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_disability_begin != ''">
      <!-- DATE - DISABILITY BEGIN - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>360*</DTP01> <!-- Date Time Qualifier - ID R3 value: 360 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_disability_begin"/>~</DTP03> <!-- Disability From Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_disability_end != ''">
      <!-- DATE - DISABILITY END - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>361*</DTP01> <!-- Date Time Qualifier - ID R3 value: 361 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_disability_end"/>~</DTP03> <!-- Disability To Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_last_work != ''">
      <!-- DATE - LAST WORKED - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>297*</DTP01> <!-- Date Time Qualifier - ID R3 value: 297 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_last_work"/>~</DTP03> <!-- Last Worked Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_auth_return_to_work != ''">
      <!-- DATE - AUTHORIZED RETURN TO WORK - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>296*</DTP01> <!-- Date Time Qualifier - ID R3 value: 296 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_auth_return_to_work"/>~</DTP03> <!-- Work Return Date - AN R1-35 format: CCYYMMDD -->
      <CLM2300.14.12>DTP*296*D8*<xsl:value-of select="patient/date_auth_return_to_work"/>~</CLM2300.14.12>
      </xsl:if>
      <xsl:if test="patient/date_of_admission != ''">
      <!-- DATE - ADMISSION - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>435*</DTP01> <!-- Date Time Qualifier - ID R3 value: 435 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_admission"/>~</DTP03> <!-- Related Hospitalization Admission Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_discharge != ''">
      <!-- DATE - DISCHARGE - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>096*</DTP01> <!-- Date Time Qualifier - ID R3 value: 096 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_discharge"/>~</DTP03> <!-- Related Hospitalization Discharge Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <xsl:if test="patient/date_of_assumed_care != ''">
      <!-- DATE - ASSUMED AND RELINQUISHED CARE DATES - 2300 -->
      <DTP>DTP*</DTP>
      <DTP01>090*</DTP01> <!-- Date Time Qualifier - ID R3 options: 090, 091 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DTP03><xsl:value-of select="patient/date_of_assumed_care"/>~</DTP03> <!-- Assumed or Relinquished Care Date - AN R1-35 format: CCYYMMDD -->
      </xsl:if>
      <!-- X12 Optional Dates End -->

      <!-- NOTES:
      PWK - CLAIM SUPPLEMENTAL INFORMATION - 2300 optional
      -->

      <xsl:if test="subscriber/contract_type_code != ''">
      <!-- CONTRACT INFORMATION - 2300 optional -->
      <CN1>CN1*</CN1>
      <CN101><xsl:value-of select="subscriber/contract_type_code"/>*</CN101> <!-- Contract Type Code - ID R2 options: 02, 03, 04, 05, 06, 09 -->
      <CN102><xsl:value-of select="subscriber/contract_amount"/>*</CN102> <!-- Contract Amount S9(7)V99 - R S1-18 -->
      <CN103><xsl:value-of select="subscriber/contract_percent"/>*</CN103> <!-- Contract Percentage 9(2)V99 - R S1-6 -->
      <CN104><xsl:value-of select="subscriber/contract_code"/>*</CN104> <!-- Contract Code - AN S1-30 -->
      <CN105><xsl:value-of select="subscriber/contract_discount_percent"/>*</CN105> <!-- Terms Discount Percent 9(2)V99 - R S1-6 -->
      <CN106><xsl:value-of select="subscriber/contract_version"/>~</CN106> <!-- Contract Version Identifier - AN S1-30 -->
      </xsl:if>

      <xsl:if test="clearing_house/credit_max_amount != ''">
      <!-- CREDIT/DEBIT CARD MAXIMUM AMOUNT - 2300 optional -->
      <AMT>AMT*</AMT>
      <AMT01>MA*</AMT01> <!-- Amount Qualifier Code - ID R1-3 -->
      <AMT02><xsl:value-of select="clearing_house/credit_max_amount"/>~</AMT02> <!-- Credit or Debit Card Maximum Amount S9(7)V99 - R R1-18 -->
      <!--<AMT03></AMT03>--> <!-- Credit/Debit Flag Code - ID N/U1 -->
      </xsl:if>

      <!-- NOTES:
      AMT - PATIENT AMOUNT PAID - 2300 optioanl
      AMT - TOTAL PURCHASED SERVICE AMOUNT - 2300 optional
      -->

      <!-- NOTES:
      REF - SERVICE AUTHORIZATION EXCEPTION CODE - 2300 optional
      REF - MANDATORY MEDICARE (SECTION 4081) CROSSOVER INDICATOR - 2300 optional
      REF - MAMMOGRAPHY CERTIFICATION NUMBER - 2300 optional
      REF - PRIOR AUTHORIZATION OR REFERRAL NUMBER - 2300 optional
      REF - ORIGINAL REFERENCE NUMBER (ICN/DCN) - 2300 optional
      -->

      <xsl:if test="billing_facility/clia_number != ''">
      <!-- CLINICAL LABORATORY IMPROVEMENT AMENDMENT (CLIA) NUMBER - 2300 optional -->
      <REF>REF*</REF>
      <REF01>X4*</REF01> <!-- Reference Identification Qualifier - ID R2-3 value: X4 -->
      <REF02><xsl:value-of select="billing_facility/clia_number"/>~</REF02> <!-- Clinical Laboratory Improvement Amendment Number - AN R1-30 -->
      <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
      <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->
      </xsl:if>

      <!-- NOTES:
      REF - REPRICED CLAIM NUMBER - 2300 optional
      REF - ADJUSTED REPRICED CLAIM NUMBER - 2300 optional
      REF - INVESTIGATIONAL DEVICE EXEMPTION NUMBER - 2300 optional
      REF - CLAIM IDENTIFICATION NUMBER FOR CLEARING HOUSES AND OTHER TRANSMISSION INTERMEDIARIES - 2300 optional
      REF - AMBULATORY PATIENT GROUP (APG) - 2300 optional
      REF - MEDICAL RECORD NUMBER - 2300 optional
      REF - DEMONSTRATION PROJECT IDENTIFIER - 2300 optional
      K3 - FILE INFORMATION - 2300 optional
      -->

      <xsl:if test="patient/comment != ''">
      <!-- CLAIM NOTE - 2300 optional -->
      <NTE>NTE*</NTE>
      <NTE01><xsl:value-of select="translate(patient/comment_type, $lowercase, $uppercase)"/>*</NTE01> <!-- Note Reference Code - ID R3 options: ADD, CER, DCP,DGN,PMT,T PO -->
      <NTE02><xsl:value-of select="translate(patient/comment, $lowercase, $uppercase)"/>~</NTE02> <!-- Claim Note Text - AN R1-80 -->
      </xsl:if>

      <!-- NOTES:
      CR1 - AMBULANCE TRANSPORT INFORMATION - 2300 optional
      CR2 - SPINAL MANIPULATION SERVICE INFORMATION - 2300 optional
      CRC - AMBULANCE CERTIFICATION - 2300 optional
      CRC - PATIENT CONDITION INFORMATION: VISION - 2300 optional
      CRC - HOMEBOUND INDICATOR - 2300 optional
      CRC - EPSDT REFERRAL - 2300 optional
      -->

      <!-- HEALTH CARE DIAGNOSIS CODE - 2300 optional -->
      <HI>HI*</HI>
      <HI01></HI01> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI01-1>BK:</HI01-1> <!-- Diagnosis Type Code - ID R1-3 value: BK -->
      <HI01-2><xsl:value-of select="claim_line/diagnosis1"/></HI01-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI01-3></HI01-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI01-4></HI01-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI01-5></HI01-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI01-6></HI01-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI01-7></HI01-7>--> <!-- Version Identifier - AN N/U1-30 -->
      <xsl:if test="claim_line/diagnosis2 != ''">
      <HI02>*</HI02> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI02-1>BF:</HI02-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI02-2><xsl:value-of select="claim_line/diagnosis2"/></HI02-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI02-3></HI02-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI02-4></HI02-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI02-5></HI02-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI02-6></HI02-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI02-7></HI02-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis3 != ''">
      <HI03>*</HI03> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI03-1>BF:</HI03-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI03-2><xsl:value-of select="claim_line/diagnosis3"/></HI03-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI03-3></HI03-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI03-4></HI03-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI03-5></HI03-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI03-6></HI03-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI03-7></HI03-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis4 != ''">
      <HI04>*</HI04> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI04-1>BF:</HI04-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI04-2><xsl:value-of select="claim_line/diagnosis4"/></HI04-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI04-3></HI04-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI04-4></HI04-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI04-5></HI04-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI04-6></HI04-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI04-7></HI04-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis5 != ''">
      <HI05>*</HI05> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI05-1>BF:</HI05-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI05-2><xsl:value-of select="claim_line/diagnosis5"/></HI05-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI05-3></HI05-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI05-4></HI05-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI05-5></HI05-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI05-6></HI05-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI05-7></HI05-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis6 != ''">
      <HI06>*</HI06> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI06-1>BF:</HI06-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI06-2><xsl:value-of select="claim_line/diagnosis6"/></HI06-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI06-3></HI06-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI06-4></HI06-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI06-5></HI06-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI06-6></HI06-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI06-7></HI06-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis7 != ''">
      <HI07>*</HI07> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI07-1>BF:</HI07-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI07-2><xsl:value-of select="claim_line/diagnosis7"/></HI07-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI07-3></HI07-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI07-4></HI07-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI07-5></HI07-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI07-6></HI07-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI07-7></HI07-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <xsl:if test="claim_line/diagnosis8 != ''">
      <HI08>*</HI08> <!-- HEALTH CARE CODE INFORMATION - R  -->
      <HI08-1>BF:</HI08-1> <!-- Diagnosis Type Code - ID R1-3 value: BF -->
      <HI08-2><xsl:value-of select="claim_line/diagnosis8"/></HI08-2> <!-- Diagnosis Code - AN R1-30 -->
      <!--<HI08-3></HI08-3>--> <!-- Date Time Period Format Qualifier - ID N/U2-3 -->
      <!--<HI08-4></HI08-4>--> <!-- Date Time Period - AN N/U1-35 -->
      <!--<HI08-5></HI08-5>--> <!-- Monetary Amount - R N/U1-18 -->
      <!--<HI08-6></HI08-6>--> <!-- Quantity - R N/U1-15 -->
      <!--<HI08-7></HI08-7>--> <!-- Version Identifier - AN N/U1-30 -->
      </xsl:if>
      <HI09>~</HI09> <!-- HEALTH CARE CODE INFORMATION - N/U  -->
      <!--<HI10></HI10>--> <!-- HEALTH CARE CODE INFORMATION - N/U  -->
      <!--<HI11></HI11>--> <!-- HEALTH CARE CODE INFORMATION - N/U  -->
      <!--<HI12></HI12>--> <!-- HEALTH CARE CODE INFORMATION - N/U  -->

      <xsl:if test="clearing_house/repricing_method != ''">
      <!-- CLAIM PRICING/REPRICING INFORMATION - 2300 optional -->
      <HCP>HCP*</HCP>
      <HCP01><xsl:value-of select="clearing_house/repricing_method"/>*</HCP01> <!-- Pricing Methodology - ID R2 options: 00, 01, 02, 03, 04, 05, 07, 08, 09, 10 ,11, 12, 13, 14 -->
      <HCP02><xsl:value-of select="clearing_house/allowed_amount"/>*</HCP02> <!-- Repriced Allowed Amount S9(7)V99 - R R1-18 -->
      <HCP03><xsl:value-of select="clearing_house/savings_amount"/>*</HCP03> <!-- Repriced Saving Amount S9(7)V99 - R S1-18 -->
      <HCP04><xsl:value-of select="clearing_house/identifier"/>*</HCP04> <!-- Repricing Organization Identifier - AN S1-30 -->
      <HCP05><xsl:value-of select="clearing_house/rate"/>*</HCP05> <!-- Repricing Per Diem or Flat Rate Amount S9(5)V99 - R S1-9 -->
      <HCP06><xsl:value-of select="clearing_house/apg_code"/>*</HCP06> <!-- Repriced Approved Ambulatory Patient Group Code - AN S1-30 -->
      <HCP07><xsl:value-of select="clearing_house/apg_amount"/>*</HCP07> <!-- Repriced Approved Ambulatory Patient Group Amount S9(7)V99 - R S1-18 -->
      <HCP08>*</HCP08> <!-- Product/Service ID - AN N/U1-48 -->
      <HCP09>*</HCP09> <!-- Product/Service ID Qualifier - ID N/U2 -->
      <HCP10>*</HCP10> <!-- Product/Service ID - AN N/U1-48 -->
      <HCP11>*</HCP11> <!-- Unit or Basis for Measurement Code - ID N/U2 -->
      <HCP12>*</HCP12> <!-- Quantity 9(3)V9 - R N/U1-15 -->
      <HCP13><xsl:value-of select="clearing_house/reject_code"/>*</HCP13> <!-- Reject Reason Code - ID S2 options: T1, T2, T3, T4, T5, T6 -->
      <HCP14><xsl:value-of select="clearing_house/compliance_code"/>*</HCP14> <!-- Policy Compliance Code - ID S1-2 options: 1, 2, 3, 4, 5 -->
      <HCP15><xsl:value-of select="clearing_house/exception_code"/>~</HCP15> <!-- Exception Code - ID S1-2 options: 1, 2, 3, 4, 5, 6 -->
      </xsl:if>

      <!-- NOTES:
      CR7 - HOME HEALTH CARE PLAN INFORMATION - 2305 optional
      HSD - HEALTH CARE SERVICES DELIVERY - 2305 optional
      -->

      <xsl:if test="referring_provider/last_name != ''">
      <!-- REFERRING PROVIDER NAME - 2310A optional -->
      <NM1>NM1*</NM1>
      <NM101><xsl:value-of select="translate(referring_provider/referral_type, $lowercase, $uppercase)"/>*</NM101> <!-- Entity Identifier Code - ID R2-3 options: DN, P3 -->
      <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1, 2 -->
      <NM103><xsl:value-of select="translate(referring_provider/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Referring Provider Last Name - AN R1-35 -->
      <NM104><xsl:value-of select="translate(referring_provider/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Referring Provider First Name - AN S1-25 -->
      <NM105>*</NM105> <!-- Referring Provider Middle Name - AN S1-25 -->
      <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
      <NM107>*</NM107> <!-- Referring Provider Name Suffix - AN S1-10 -->
      <NM108><xsl:value-of select="translate(referring_provider/identifier_type, $lowercase, $uppercase)"/>*</NM108> <!-- Identification Code Qualifier - ID S1-2 options: 24, 34, XX -->
      <NM109><xsl:value-of select="translate(referring_provider/identifier, $lowercase, $uppercase)"/>~</NM109> <!-- Referring Provider Identifier - AN S2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->
      </xsl:if>

      <xsl:if test="referring_provider/taxonomy_code != ''">
      <!-- REFERRING PROVIDER SPECIALTY INFORMATION - 2310A optional -->
      <PRV>PRV*</PRV>
      <PRV01>RF*</PRV01> <!-- Provider Code - ID R1-3 value: RF -->
      <PRV02>ZZ*</PRV02> <!-- Reference Identification Qualifier - ID R2-3 value: ZZ -->
      <PRV03><xsl:value-of select="translate(referring_provider/taxonomy_code, $lowercase, $uppercase)"/>~</PRV03> <!-- Provider Taxonomy Code - AN R1-30 -->
      <!--<PRV04></PRV04>--> <!-- State or Province Code - ID N/U2 -->
      <!--<PRV05></PRV05>--> <!-- PROVIDER SPECIALTY INFORMATION - N/U -->
      <!--<PRV06></PRV06>--> <!-- Provider Organization Code - ID N/U3 -->
      </xsl:if>

      <!-- NOTES:
      REF - REFERRING PROVIDER SECONDARY IDENTIFICATION - 2310A optional
      -->

      <xsl:if test="provider/last_name != ''">
      <!-- RENDERING PROVIDER NAME - 2310B optional -->
      <NM1>NM1*</NM1>
      <NM101>82*</NM101> <!-- Entity Identifier Code - ID R2-3 value: 82 -->
      <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1, 2 -->
      <NM103><xsl:value-of select="translate(provider/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Rendering Provider Last or Organization Name - AN R1-35 -->
      <NM104><xsl:value-of select="translate(provider/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Rendering Provider First Name - AN S1-25 -->
      <NM105>*</NM105> <!-- Rendering Provider Middle Name - AN S1-25 -->
      <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
      <NM107>*</NM107> <!-- Rendering Provider Name Suffix - AN S1-10 -->
      <NM108><xsl:value-of select="translate(provider/identifier_type, $lowercase, $uppercase)"/>*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: 24, 34, XX -->
      <NM109><xsl:value-of select="translate(provider/identifier, $lowercase, $uppercase)"/>~</NM109> <!-- Rendering Provider Identifier - AN R2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

      <!-- NOTES:
      PRV - RENDERING PROVIDER SPECIALTY INFORMATION - 2310B optional
      -->

      <xsl:if test="provider/identifier_2 != ''">
      <!-- RENDERING PROVIDER SECONDARY IDENTIFICATION - 2310B optional -->
      <REF>REF*</REF>
      <REF01>1C*</REF01> <!-- Reference Identification Qualifier - ID R2-3 options: 0B, 1B, 1C, 1D, 1G, 1H, EI, G2, LU, N5, SY, X5 -->
      <REF02><xsl:value-of select="translate(provider/identifier_2, $lowercase, $uppercase)"/>~</REF02> <!-- Rendering Provider Secondary Identifier - AN R1-30 -->
      <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
      <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->
      </xsl:if>

      </xsl:if>

      <!-- NOTES:
      NM1 - PURCHASED SERVICE PROVIDER NAME - 2310C optional
      REF - PURCHASED SERVICE PROVIDER SECONDARY IDENTIFICATION - 2310C optional
      -->

      <!-- SERVICE FACILITY LOCATION - 2310D optional -->
      <NM1>NM1*</NM1>
      <NM101>FA*</NM101> <!-- Entity Identifier Code - ID R2-3 options: 77, FA, LI, TL -->
      <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 value: 2 -->
      <NM103><xsl:value-of select="translate(treating_facility/name, $lowercase, $uppercase)"/>~</NM103> <!-- Laboratory or Facility Name - AN S1-35 -->
      <!--<NM104></NM104>--> <!-- Name First - AN N/U1-25 -->
      <!--<NM105></NM105>--> <!-- Name Middle - AN N/U1-25 -->
      <!--<NM106></NM106>--> <!-- Name Prefix - AN N/U1-10 -->
      <!--<NM107></NM107>--> <!-- Name Suffix - AN N/U1-10 -->
      <!--<NM108></NM108>--> <!-- Identification Code Qualifier - ID S1-2 options: 24, 34, XX -->
      <!--<NM109></NM109>--> <!-- Laboratory or Facility Primary Identifier - AN S2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

      <!-- SERVICE FACILITY LOCATION ADDRESS - 2310D -->
      <N3>N3*</N3>
      <N301><xsl:value-of select="translate(treating_facility/address/line1, $lowercase, $uppercase)"/></N301> <!-- Laboratory or Facility Address Line - AN R1-55 -->
      <N302><xsl:if test="treating_facility/address/line2 != ''">*<xsl:value-of select="translate(treating_facility/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Laboratory or Facility Address Line - AN S1-55 -->

      <!-- SERVICE FACILITY LOCATION CITY/STATE/ZIP - 2310D -->
      <N4>N4*</N4>
      <N401><xsl:value-of select="translate(treating_facility/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Laboratory or Facility City Name - AN R2-30 -->
      <N402><xsl:value-of select="translate(treating_facility/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Laboratory or Facility State or Province Code - ID R2 -->
      <N403><xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="treating_facility/address/zip"/>
        <xsl:with-param name="length" select="'5'"/>
      </xsl:call-template>~</N403> <!-- Laboratory or Facility Postal Zone ZIP Code - ID R3-15 -->
      <!--<N404></N404>--> <!-- Laboratory/Facility Country Code - ID S2-3 -->
      <!--<N405></N405>--> <!-- Location Qualifier - ID N/U1-2 -->
      <!--<N406></N406>--> <!-- Location Identifier - AN N/U1-30 -->

      <!-- NOTES:
      REF - SERVICE FACILITY LOCATION SECONDARY IDENTIFICATION - 2310D optional
      -->

      <xsl:if test="supervising_provider/last_name != ''">
      <!-- SUPERVISING PROVIDER NAME - 2310E optional -->
      <NM1>NM1*</NM1>
      <NM101>DQ*</NM101> <!-- Entity Identifier Code - ID R2-3 value: DQ -->
      <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 value: 1 -->
      <NM103><xsl:value-of select="translate(supervising_provider/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Supervising Provider Last Name - AN R1-35 -->
      <NM104><xsl:value-of select="translate(supervising_provider/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Supervising Provider First Name - AN R1-25 -->
      <NM105>*</NM105> <!-- Supervising Provider Middle Name - AN S1-25 -->
      <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
      <NM107>*</NM107> <!-- Supervising Provider Name Suffix - AN S1-10 -->
      <NM108><xsl:value-of select="translate(supervising_provider/identifier_type, $lowercase, $uppercase)"/>*</NM108> <!-- Identification Code Qualifier - ID S1-2 options: 24, 34, XX -->
      <NM109><xsl:value-of select="translate(supervising_provider/identifier, $lowercase, $uppercase)"/>~</NM109> <!-- Supervising Provider Identifier - AN S2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->
      </xsl:if>

      <!-- NOTES:
      REF - SUPERVISING PROVIDER SECONDARY IDENTIFIER - 2310E optional
      -->

      <xsl:if test="payer2">
      <!-- OTHER SUBSCRIBER INFORMATION - 2320 optional -->
      <SBR>SBR*</SBR>
      <SBR01>S*</SBR01> <!-- Payer Responsibility Sequence Number Code - ID R1 options: P, S, T -->
      <SBR02>18*</SBR02> <!-- Individual Relationship Code - ID R2 options: 01, 04, 05, 07, 10, 15, 17, 18, 19, 20, 21, 22, 23, 24, 29, 32, 33, 36, 39, 40, 41, 43, 53, G8 -->
      <SBR03>*</SBR03> <!-- Insured Group or Policy Number - AN S1-30 -->
      <SBR04>*</SBR04> <!-- Other Insured Group Name - AN S1-60 -->
      <SBR05>MI*</SBR05> <!-- Insurance Type Code - ID R1-3 options: AP, C1, CP, GP, HM, IP, LD, LT, MB, MC, MI, MP, OT, PP, SP -->
      <SBR06>*</SBR06> <!-- Coordination of Benefits Code - ID N/U1 -->
      <SBR07>*</SBR07> <!-- Yes/No Condition or Response Code - ID N/U1 -->
      <SBR08>*</SBR08> <!-- Employment Status Code - ID N/U2 -->
      <SBR09>16~</SBR09> <!-- Claim Filing Indicator Code - ID S1-2 options: 09, 10, 11, 12, 13, 14, 15, 16, AM, BL, CH, CI, DS, HM, LI, LM, MB, MC, OF, TV, VA, WC, ZZ -->

      <!-- NOTES:
      CAS - CLAIM LEVEL ADJUSTMENTS - 2320 optional
      AMT - COB	PAYER PAID AMOUNT - 2320 optional
      AMT - COB APPROVED AMOUNT - 2320 optional
      AMT - COB ALLOWED AMOUNT  - 2320 optional
      AMT - COB PATIENT RESPONSIBILITY AMOUNT - 2320 optional
      AMT - COB COVERED AMOUNT - 2320 optional
      AMT - COB DISCOUNT AMOUNT - 2320 optional
      AMT - COB PER DAY LIMIT AMOUNT - 2320 optional
      AMT - COB PATIENT PAID AMOUNT - 2320 optional
      AMT - COB TAX AMOUNT - 2320 optional
      AMT - COB TOTAL CLAIM BEFORE TAXES AMOUNT - 2320 optional
      -->

      <!-- SUBSCRIBER DEMOGRAPHIC INFORMATION - 2320 optional -->
      <DMG>DMG*</DMG>
      <DMG01>D8*</DMG01> <!-- Date Time Period Format Qualifier - ID R2-3 value: D8 -->
      <DMG02><xsl:value-of select="translate(subscriber/date_of_birth, $lowercase, $uppercase)"/>*</DMG02> <!-- Other Insured Birth Date - AN R1-35 format: CCYYMMDD -->
      <DMG03><xsl:value-of select="translate(subscriber/gender, $lowercase, $uppercase)"/>~</DMG03> <!-- Other Insured Gender Code - ID R1 options: F, M, U -->
      <!--<DMG04></DMG04>--> <!-- Marital Status Code - ID N/U1 -->
      <!--<DMG05></DMG05>--> <!-- Race or Ethnicity Code - ID N/U1 -->
      <!--<DMG06></DMG06>--> <!-- Citizenship Status Code - ID N/U1-2 -->
      <!--<DMG07></DMG07>--> <!-- Country Code - ID N/U2-3 -->
      <!--<DMG08></DMG08>--> <!-- Basis of Verification Code - ID N/U1-2 -->
      <!--<DMG09></DMG09>--> <!-- Quantity - R N/U1-15 -->


      <!-- OTHER INSURANCE COVERAGE INFORMATION - 2320 -->
      <OI>OI*</OI>
      <OI01>*</OI01> <!-- Claim Filing Indicator Code - ID N/U1-2 -->
      <OI02>*</OI02> <!-- Claim Submission Reason Code - ID N/U2 -->
      <OI03>Y*</OI03> <!-- Benefits Assignment Certification Indicator - ID R1 options: N, Y -->
      <OI04>B*</OI04> <!-- Patient Signature Source Code - ID S1 options: B, C, M, P, S -->
      <OI05>*</OI05> <!-- Provider Agreement Code - ID N/U1 -->
      <OI06>Y~</OI06> <!-- Release of Information Code - ID R1 options: A, I, M, N, O, Y -->

      <!-- NOTES:
      MOA - MEDICARE OUTPATIENT ADJUDICATION INFORMATION - 2320 optional
      -->

      <!-- OTHER SUBSCRIBER NAME - 2330A -->
      <NM1>NM1*</NM1>
      <NM101>IL*</NM101> <!-- Entity Identifier Code - ID R2-3 value: IL -->
      <NM102>1*</NM102> <!-- Entity Type Qualifier - ID R1 options: 1, 2 -->
      <NM103><xsl:value-of select="translate(subscriber/last_name, $lowercase, $uppercase)"/>*</NM103> <!-- Other Insured Last Name - AN R1-35 -->
      <NM104><xsl:value-of select="translate(subscriber/first_name, $lowercase, $uppercase)"/>*</NM104> <!-- Other Insured First Name - AN S1-25 -->
      <NM105><xsl:value-of select="translate(subscriber/middle_name, $lowercase, $uppercase)"/>*</NM105> <!-- Other Insured Middle Name - AN S1-25 -->
      <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
      <NM107>*</NM107> <!-- Other Insured Name Suffix - AN S1-10 -->
      <NM108>MI*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: MI, ZZ -->
      <NM109><xsl:value-of select="translate(subscriber/id, $lowercase, $uppercase)"/>~</NM109> <!-- Other Insured Identifier - AN R2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

      <!-- OTHER SUBSCRIBER ADDRESS - 2330A optional -->
      <N3>N3*</N3>
      <N301><xsl:value-of select="translate(subscriber/address/line1, $lowercase, $uppercase)"/></N301> <!-- Other Insured Address Line - AN R1-55 -->
      <N302><xsl:if test="subscriber/address/line2 != ''">*<xsl:value-of select="translate(subscriber/address/line2, $lowercase, $uppercase)"/></xsl:if>~</N302> <!-- Other Insured Address Line - AN S1-55 -->

      <!-- OTHER SUBSCRIBER CITY/STATE/ZIP CODE - 2330A optional -->
      <N4>N4*</N4>
      <N401><xsl:value-of select="translate(subscriber/address/city, $lowercase, $uppercase)"/>*</N401> <!-- Other Insured City Name - AN S2-30 -->
      <N402><xsl:value-of select="translate(subscriber/address/state, $lowercase, $uppercase)"/>*</N402> <!-- Other Insured State Code - ID S2 -->
      <N403><xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="subscriber/address/zip"/>
        <xsl:with-param name="length" select="'5'"/>
      </xsl:call-template>~</N403> <!-- Other Insured Postal Zone or ZIP Code - ID S3-15 -->
      <N404></N404> <!-- Subscriber Country Code - ID S2-3 -->
      <N405></N405> <!-- Location Qualifier - ID N/U1-2 -->
      <N406></N406> <!-- Location Identifier - AN N/U1-30 -->

      <!-- NOTES:
      REF - OTHER SUBSCRIBER SECONDARY IDENTIFICATION - 2330A optional
      -->

      <!-- OTHER PAYER NAME - 2330B -->
      <NM1>NM1*</NM1>
      <NM101>PR*</NM101> <!-- Entity Identifier Code - ID R2-3 value: PR -->
      <NM102>2*</NM102> <!-- Entity Type Qualifier - ID R1 value: 2 -->
      <NM103><xsl:value-of select="translate(payer2/name, $lowercase, $uppercase)"/>*</NM103> <!-- Other Payer Last or Organization Name - AN R1-35 -->
      <NM104>*</NM104> <!-- Name First - AN N/U1-25 -->
      <NM105>*</NM105> <!-- Name Middle - AN N/U1-25 -->
      <NM106>*</NM106> <!-- Name Prefix - AN N/U1-10 -->
      <NM107>*</NM107> <!-- Name Suffix - AN N/U1-10 -->
      <NM108>PI*</NM108> <!-- Identification Code Qualifier - ID R1-2 options: PI, XV -->
      <NM109><xsl:value-of select="translate(payer2/id, $lowercase, $uppercase)"/>~</NM109> <!-- Other Payer Primary Identifier - AN R2-80 -->
      <!--<NM110></NM110>--> <!-- Entity Relationship Code - ID N/U2 -->
      <!--<NM111></NM111>--> <!-- Entity Identifier Code - ID N/U2-3 -->

      </xsl:if>

      <!-- NOTES:
      PER - OTHER PAYER CONTACT INFORMATION - 2330B optional
      DTP - CLAIM ADJUDICATION DATE - 2330B optional
      REF - OTHER PAYER SECONDARY IDENTIFIER - 2330B optional
      REF - OTHER PAYER PRIOR AUTHORIZATION OR REFERRAL NUMBER - 2330B optional
      REF - OTHER PAYER CLAIM ADJUSTMENT INDICATOR - 2330B optional

      NM1 - OTHER PAYER PATIENT INFORMATION - 2330C optional
      REF - OTHER PAYER PATIENT IDENTIFICATION - 2330C optional

      NM1 - OTHER PAYER REFERRING PROVIDER - 2330D optional
      *REF - OTHER PAYER REFERRING PROVIDER IDENTIFICATION - 2330D

      NM1 - OTHER PAYER RENDERING PROVIDER - 2330E optional
      *REF - OTHER PAYER RENDERING PROVIDER SECONDARY IDENTIFICATION - 2330E

      NM1 - OTHER PAYER PURCHASED SERVICE PROVIDER - 2330F optional
      *REF - OTHER PAYER PURCHASED SERVICE PROVIDER IDENTIFICATION - 2330F

      NM1 - OTHER PAYER SERVICE FACILITY LOCATION - 2330G optional
      *REF - OTHER PAYER SERVICE FACILITY LOCATION IDENTIFICATION - 2330G

      NM1 - OTHER PAYER SUPERVISING PROVIDER - 2330H optional
      *REF - OTHER PAYER SUPERVISING PROVIDER IDENTIFICATION - 2330H
      -->

      <!-- SERVICE LINE - 2400 -->
      <LX>LX*</LX>
      <LX01>1~</LX01> <!-- Assigned Number - N0 R1-6 -->

      <!-- PROFESSIONAL SERVICE - 2400 -->
      <SV1>SV1*</SV1>
      <SV101></SV101> <!-- COMPOSITE MEDICAL PROCEDURE IDENTIFIER - R -->
      <SV101-1>HC:</SV101-1> <!-- Product or Service ID Qualifier - ID R2 options: HC, IV, ZZ -->
      <SV101-2><xsl:value-of select="translate(claim_line/procedure, $lowercase, $uppercase)"/></SV101-2> <!-- Procedure Code - AN R1-48 -->
      <SV101-3><xsl:if test="claim_line/modifier1 != ''">:<xsl:value-of select="translate(claim_line/modifier1, $lowercase, $uppercase)"/></xsl:if></SV101-3> <!-- Procedure Modifier - AN S2 -->
      <SV101-4><xsl:if test="claim_line/modifier2 != ''">:<xsl:value-of select="translate(claim_line/modifier2, $lowercase, $uppercase)"/></xsl:if></SV101-4> <!-- Procedure Modifier - AN S2 -->
      <SV101-5><xsl:if test="claim_line/modifier3 != ''">:<xsl:value-of select="translate(claim_line/modifier3, $lowercase, $uppercase)"/></xsl:if></SV101-5> <!-- Procedure Modifier - AN S2 -->
      <SV101-6><xsl:if test="claim_line/modifier4 != ''">:<xsl:value-of select="translate(claim_line/modifier4, $lowercase, $uppercase)"/></xsl:if></SV101-6> <!-- Procedure Modifier - AN S2 -->
      <SV101-7>*</SV101-7> <!-- Description - AN N/U1-80 -->
      <SV102><xsl:choose>
          <xsl:when test="claim_line/amount = '0.00'">0</xsl:when>
          <xsl:otherwise><xsl:value-of select="claim_line/amount"/></xsl:otherwise>
        </xsl:choose>*</SV102> <!-- Line Item Charge Amount S9(7)V99 - R R1-18 -->
      <SV103>UN*</SV103> <!-- Unit or Basis for Measurement Code - ID R2 options: F2,MJ,UN -->
      <SV104><xsl:value-of select="claim_line/units"/>*</SV104> <!-- Service Unit Count "F2" = 9(7)V999 "MJ" = 9(4) "UN" = 9(3)V9 - R R1-15 -->
      <SV105><xsl:value-of select="treating_facility/facility_code"/>*</SV105> <!-- Place of Service Code - AN S1-2 options: 11, 12, 21, 22, 23, 24, 25, 26, 31, 32, 33, 34, 41, 42, 50, 51, 52, 53, 54, 55, 56, 60, 61, 62, 65, 71, 72, 81, 99 -->
      <SV106>*</SV106> <!-- Service Type Code - ID N/U1-2 -->
      <SV107>1</SV107> <!-- COMPOSITE DIAGNOSIS CODE POINTER - S -->
      <SV107-1><xsl:if test="claim_line/diagnosis2 != ''">:2</xsl:if><xsl:if test="claim_line/diagnosis3 != ''">:3</xsl:if></SV107-1> <!-- Diagnosis Code Pointer - N0 R1-2 -->
      <SV107-2><xsl:if test="claim_line/diagnosis4 != ''">:4</xsl:if><xsl:if test="claim_line/diagnosis5 != ''">:5</xsl:if></SV107-2> <!-- Diagnosis Code Pointer - N0 S1-2 -->
      <SV107-3><xsl:if test="claim_line/diagnosis6 != ''">:6</xsl:if><xsl:if test="claim_line/diagnosis7 != ''">:7</xsl:if></SV107-3> <!-- Diagnosis Code Pointer - N0 S1-2 -->
      <SV107-4><xsl:if test="claim_line/diagnosis8 != ''">:8</xsl:if></SV107-4> <!-- Diagnosis Code Pointer - N0 S1-2 -->
      <SV108>~</SV108> <!-- Monetary Amount - R N/U1-18 -->
      <!--<SV109></SV109>--> <!-- Emergency Indicator - ID S1 value: Y -->
      <!--<SV110></SV110>--> <!-- Multiple Procedure Code - ID N/U1-2 -->
      <!--<SV111></SV111>--> <!-- EPSDT Indicator - ID S1 value: Y -->
      <!--<SV112></SV112>--> <!-- Family Planning Indicator - ID S1 value: Y -->
      <!--<SV113></SV113>--> <!-- Review Code - ID N/U1-2 -->

      <!-- NOTES:
      SV5 - DURABLE MEDICAL EQUIPMENT SERVICE - 2400 optional
      PWK - DMERC CMN INDICATOR - 2400 optional
      CR1 - AMBULANCE TRANSPORT INFORMATION - 2400 optional
      CR2 - SPINAL MANIPULATION SERVICE INFORMATION - 2400 optional
      CR3 - DURABLE MEDICAL EQUIPMENT CERTIFICATION - 2400 optional
      CR5 - HOME OXYGEN THERAPY INFORMATION - 2400 optional
      CRC - AMBULANCE CERTIFICATION - 2400 optional
      CRC - HOSPICE EMPLOYEE INDICATOR - 2400 optional
      CRC - DMERC CONDITION INDICATOR - 2400 optional
      -->

      <!-- DATE - SERVICE DATE - 2400 -->
      <DTP>DTP*</DTP>
      <DTP01>472*</DTP01> <!-- Date Time Qualifier - ID R3 value: 472 -->
      <DTP02>D8*</DTP02> <!-- Date Time Period Format Qualifier - ID R2-3 options: D8, RD8 -->
      <DTP03><xsl:value-of select="claim_line/date_of_treatment"/>~</DTP03> <!-- Service Date - AN R1-35 format: CYYMMDD, CCYYMMDDCCYYMMDD -->

      <!-- NOTES:
      DTP - DATE - CERTIFICATION REVISION DATE - 2400 optional
      DTP - DATE - BEGIN THERAPY DATE - 2400 optional
      DTP - DATE - LAST CERTIFICATION DATE - 2400 optional
      DTP - DATE - DATE LAST SEEN - 2400 optional
      DTP - DATE - TEST - 2400 optional
      DTP - DATE - OXYGEN SATURATION/ARTERIAL BLOOD GAS TEST - 2400 optional
      DTP - DATE - SHIPPED - 2400 optional
      DTP - DATE - ONSET OF CURRENT SYMPTOM/ILLNESS - 2400 optional
      DTP - DATE - LAST X-RAY - 2400 optional
      DTP - DATE - ACUTE MANIFESTATION - 2400 optional
      DTP - DATE - INITIAL TREATMENT - 2400 optional
      DTP - DATE - SIMILAR ILLNESS/SYMPTOM ONSET - 2400 optional
      MEA - TEST RESULTS - 2400 optional
      CN1 - CONTRACT INFORMATION - 2400 optional
      REF - REPRICED LINE ITEM REFERENCE NUMBER - 2400 optional
      REF - ADJUSTED REPRICED LINE ITEM REFERENCE NUMBER - 2400 optional
      REF - PRIOR AUTHORIZATION OR REFERRAL NUMBER - 2400 optional
      REF - LINE ITEM CONTROL NUMBER - 2400 optional
      REF - MAMMOGRAPHY CERTIFICATION NUMBER - 2400 optional
      -->

      <xsl:if test="claim_line/clia_number != ''">
      <!-- CLINICAL LABORATORY IMPROVEMENT AMENDMENT (CLIA) IDENTIFICATION - 2400 optional -->
      <REF>REF*</REF>
      <REF01>X4*</REF01> <!-- Reference Identification Qualifier - ID R2-3 value: X4 -->
      <REF02><xsl:value-of select="translate(claim_line/clia_number, $lowercase, $uppercase)"/>~</REF02> <!-- Clinical Laboratory Improvement Amendment Number - AN R1-30 -->
      <!--<REF03></REF03>--> <!-- Description - AN N/U1-80 -->
      <!--<REF04></REF04>--> <!-- REFERENCE IDENTIFIER - N/U -->
      </xsl:if>

      <!-- NOTES:
      REF - REFERRING CLINICAL LABORATORY IMPROVEMENT AMENDMENT (CLIA) FACILITY IDENTIFICATION - 2400 optional
      REF - IMMUNIZATION BATCH NUMBER - 2400 optional
      REF - AMBULATORY PATIENT GROUP (APG) - 2400 optional
      REF - OXYGEN FLOW RATE - 2400 optional
      REF - UNIVERSAL PRODUCT NUMBER (UPN) - 2400 optional
      AMT - SALES TAX AMOUNT - 2400 optional
      AMT - APPROVED AMOUNT - 2400 optional
      AMT - POSTAGE CLAIMED AMOUNT - 2400 optional
      K3 - FILE INFORMATION - 2400 optional
      NTE - LINE NOTE - 2400 optional
      PS1 - PURCHASED SERVICE INFORMATION - 2400 optional
      HSD - HEALTH CARE SERVICES DELIVERY - 2400 optional
      HCP - LINE PRICING/REPRICING INFORMATION - 2400 optional
      LIN - DRUG IDENTIFICATION - 2410 optional
      CTP - DRUG PRICING - 2410 optional
      REF - PRESCRIPTION NUBER - 2410 optional

      NM1 - RENDERING PROVIDER NAME - 2420A optional
      PRV - RENDERING PROVIDER SPECIALTY INFORMATION - 2420A optional
      REF - RENDERING PROVIDER SECONDARY IDENTIFICATION - 2420A optional

      NM1 - PURCHASED SERVICE PROVIDER NAME - 2420B optional
      REF - PURCHASED SERVICE PROVIDER SECONDARY IDENTIFICATION - 2420B optional

      NM1 - SERVICE FACILITY LOCATION - 2420C optional
      *N3 - SERVICE FACILITY LOCATION ADDRESS - 2420C
      *N4 - SERVICE FACILITY LOCATION CITY/STATE/ZIP - 2420C
      REF - SERVICE FACILITY LOCATION SECONDARY IDENTIFICATION - 2420C optional

      NM1 - SUPERVISING PROVIDER NAME - 2420D optional
      REF - SUPERVISING PROVIDER SECONDARY IDENTIFICATION - 2420D optional

      NM1 - ORDERING PROVIDER NAME - 2420E optional
      N3 - ORDERING PROVIDER ADDRESS - 2420E optional
      N4 - ORDERING PROVIDER CITY/STATE/ZIP CODE - 2420E optional
      REF - ORDERING PROVIDER SECONDARY IDENTIFICATION - 2420E optional
      PER - ORDERING PROVIDER CONTACT INFORMATION - 2420E optional

      NM1 - REFERRING PROVIDER NAME - 2420F optional
      PRV - REFERRING PROVIDER SPECIALTY INFORMATION - 2420F optional
      REF - REFERRING PROVIDER SECONDARY IDENTIFICATION - 2420F optional

      NM1 - OTHER PAYER PRIOR AUTHORIZATION OR REFERRAL NUMBER - 2420G optional
      *REF - OTHER PAYER PRIOR AUTHORIZATION OR REFERRAL NUMBER - 2420G

      SVD - LINE ADJUDICATION INFORMATION - 2430 optional
      CAS - LINE ADJUSTMENT - 2430 optional
      *DTP - LINE ADJUDICATION DATE - 2430

      LQ - FORM IDENTIFICATION CODE - 2440 optional
      FRM - SUPPORTING DOCUMENTATION - 2440 optional
      -->
    </xsl:for-each>
  </xsl:for-each>
</xsl:template>
<!-- TEMPLATE: HL END -->




<!-- UTILITIES: STRING -->
<xsl:template name="str-remove-left">
  <xsl:param name="input"/><!-- the input string -->
  <xsl:param name="length"/><!-- pad length -->
  <xsl:param name="string" select="' '"/><!-- pad string/s -->
  <xsl:choose>
    <xsl:when test="string-length($input) &lt; $length">
      <xsl:call-template name="str-pad-left">
        <xsl:with-param name="input" select="concat($string, $input)"/>
        <xsl:with-param name="length" select="$length"/>
        <xsl:with-param name="string" select="$string"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="substring($input, string-length($input) - $length + 1)"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>
<xsl:template name="str-pad-left">
  <xsl:param name="input"/><!-- the input string -->
  <xsl:param name="length"/><!-- pad length -->
  <xsl:param name="string" select="' '"/><!-- pad string/s -->
  <xsl:choose>
    <xsl:when test="string-length($input) &lt; $length">
      <xsl:call-template name="str-pad-left">
        <xsl:with-param name="input" select="concat($string, $input)"/>
        <xsl:with-param name="length" select="$length"/>
        <xsl:with-param name="string" select="$string"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="substring($input, string-length($input) - $length + 1)"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="str-pad-right">
  <xsl:param name="input"/><!-- the input string -->
  <xsl:param name="length"/><!-- pad length -->
  <xsl:param name="string" select="' '"/><!-- pad string/s -->
  <xsl:choose>
    <xsl:when test="string-length($input) &lt; $length">
      <xsl:call-template name="str-pad-right">
        <xsl:with-param name="input" select="concat($input, $string)"/>
        <xsl:with-param name="length" select="$length"/>
        <xsl:with-param name="string" select="$string"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="substring($input, 1, $length)"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="str-pad-both">
  <xsl:param name="input"/><!-- the input string -->
  <xsl:param name="length"/><!-- pad length -->
  <xsl:param name="string" select="' '"/><!-- pad string/s -->
  <xsl:choose>
    <xsl:when test="string-length($input) &lt; $length">
      <xsl:call-template name="str-pad-both">
        <xsl:with-param name="input" select="concat($string, $input, $string)"/>
        <xsl:with-param name="length" select="$length"/>
        <xsl:with-param name="string" select="$string"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="substring($input, string-length($input) - $length + 1)"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


</xsl:stylesheet>
