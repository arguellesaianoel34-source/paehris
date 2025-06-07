<?php
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/
//exit();
$date = date('F j, Y');
if (isset($id)) {
    $customer_plan_details = $this->db->select()
        ->from('customer_plan_details')
        ->where(array('appid' => $id, 'status !=' => 0))
        ->get()->row();

    //echo $this->db->last_query();
} else {
    if ($app) {
        $plan_details = array();
        $plan_details['standard'] = ($app->systemtype == 1) ? $app->systemtype : 0;
        $app->appname = (isset($app->no_person) && $app->no_person) ? false : ucwords($app->lastname.', '.$app->firstname.' '.$app->middlename[0].'.');
        $app->address = ucwords(strtolower($app->addrspecific));
        if ($app->systemtype == 1) {
            $sysize = $this->db->select()
                ->from('customer_system_size')
                ->where('sysid',$app->newsize)
                ->get()->row();

            $app->systemsizename = ($sysize) ? $sysize->descs : 'N/A';
        } else {
            $app->systemsizename = $app->newsize;
        }
        $customer_plan_details = (object)$plan_details;
    }
}

if ($customer_plan_details) {
    if (isset($id)) {
        if ($customer_plan_details->standard) {
            $plan_qry = $this->db->select()
                ->from('customer_standard_system_rates')
                ->where(array('sysid' => $customer_plan_details->rateid))
                ->get()->row();
        } else {

            //query from non-standard table
            $plan_qry = $this->db->select()
                ->from('customer_nonstandard_system_rates')
                ->where(array('appid' => $id, 'status' => 1))
                ->order_by('datecreated','DESC')
                ->get()->row();
        }
    } else {
        if ($customer_plan_details->standard) {
            $plan_qry = $this->db->select()
                ->from('customer_standard_system_rates')
                ->where(array('systemsizeid' => $app->newsize, 'years' => $app->plantype))
                ->get()->row();
            echo 'CPD Standard: '. $this->db->last_query();
        } else {
            $plan_arr = array(
                'years' => $app->plantype,
                'monthlyamt' => $app->price,
            );
            $plan_qry = (object)$plan_arr;
            echo 'CPD Non-Standard';
        }
    }

    $plan = $plan_qry;
    if ($plan && $plan->years != 0) {
        $monthly = 0;

        if (isset($id)) {
            $billing = $this->db->select()
                ->from('customer_billing_group')
                ->where(array('appid' => $id, 'status' => 1))
                ->get()->row();
        } else {
            $billing = false;
            $date = $app->installdate;
        }

        $date = ($billing) ? date('F j, Y', strtotime($billing->installdate)) : date('F j, Y', strtotime($date . ' +1 day'));
        $monthly = ($billing) ? ordinal($billing->billfrequency) : date('jS', strtotime($date . ' +1 day'));
        if (isset($billingstart) && $billing) {
            $installmonth = date('n', strtotime($billing->installdate));
            $year = ($billingstart < $installmonth) ? date('Y') + 1 : date('Y');
            $execdate = date('F j, Y', strtotime($billingstart . '/' . ($billing->billfrequency ? $billing->billfrequency : 1) . '/' . $year));
            $echo = array('billingstart' => $billingstart, 'installmonth' => $installmonth, 'year' => $year, 'execdate' => $execdate);
            /*echo "<pre>";
            print_r ($echo);
            echo "</pre>";*/
        } else {
            $execdate = $date;
        }

        $planyear = array(
            2 => 'TWO',
            5 => 'FIVE',
            10 => 'TEN',
        );

        if (isset($app->corpname)) {
            $corpname = $app->corpname;
            $corpname .= (isset($app->corpbranch) && $app->corpbranch != '') ? ' (' . $app->corpbranch . ')' : '';
        }

        //$formdata = $params;
        /*echo "<pre>";
        print_r ($app);
        echo "</pre>";*/

        ?>
        <html>
        <head>
            <title></title>
            <style>

                html {
                    margin: 72px;
                }

                body {
                    font-family: Arial, Verdana, sans-serif;
                    font-size: 10.75pt;
                <?php if ($plan->years == 10) {?>
                    line-height: 1.6;
                <?php } else { ?>
                    line-height: 1.25;
                <?php } ?>
                }

                span.data {
                    font-weight: bold;
                    color: #2E74B5;
                }

                span.lead {
                    width: 20%;
                }

                p {
                    width: 100%;
                }

                p.paragraph {
                    text-indent: 30px;
                    text-align: justify;
                }

                .center {
                    text-align: center;
                }

                .bold {
                    font-weight: bold;
                }

                table {
                    width: 100%;
                    margin-bottom: 1rem;
                    background-color: transparent;
                    border-collapse: collapse;
                }

                table.bordered td {
                    border: 1px solid black;
                }

                td {
                    padding: 5px;
                }

                .leading {
                    width: 31%;
                }

                ul {
                    padding-left: 20px;
                    text-indent: 2px;
                    list-style: none;
                    list-style-position: outside;
                }

                ol {
                    counter-reset: item;
                    list-style-position: outside;
                }

                body > ol {
                    margin-left: -15px;
                }

                li {
                    text-align: justify;
                    margin-right: 0em;
                    counter-increment: item;
                    display: block;
                }

                ol>li:before {
                    content: counters(item, ".") ". ";
                    display: inline-block;
                    text-align: right;
                    width: 20px;
                    margin-left: -25px !important;
                    margin-right: 5px !important;
                }

                li>ol>li:before {
                    font-weight: bold;
                }

                footer {
                    position: fixed;
                    left: 20px;
                    bottom: 0;
                    text-align: right;
                }
                footer .page:after {
                    content: counter(page);
                    font-size: 12pt;
                }

                .pages:after {
                    content: counter(page);
                }

                ol li ol.letterlist {
                    counter-reset: letter;
                }

                ol.letterlist > li {
                    list-style: none;
                    position: relative;
                }

                ol.letterlist > li:before {
                    counter-increment: item -1 letter;
                    content: counter(letter,lower-alpha) ". ";
                    position: absolute;
                }

                .page_break {
                    page-break-inside: avoid;
                }

            </style>
        </head>
        <body>
        <footer>
            <p class="page"></p>
        </footer>
        <p class="center bold">LEASE TO OWN AGREEMENT</p>
        <table class="bordered">
            <tr>
                <td colspan="2">
                    This Agreement is made this <?php echo ordinal_date($date); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Between the following parties (&#8220;Parties&#8221;)
                </td>
            </tr>
            <tr>
                <td style="width: 25%">
                    (1) LESSOR
                </td>
                <td>
                    <b>Company Name:</b> PANAY ALTERNATIVE ENERGY INC.
                </td>
            </tr>
            <tr>
                <td style="width: 25%"></td>
                <td>
                    <b>Address:</b> Jaro, Iloilo City
                </td>
            </tr>
            <tr>
                <td style="width: 25%">
                    (2) LESSEE
                </td>
                <td>
                    <b>Name:</b> <?php echo ($app->apptype == 1) ? ucwords(strtolower($app->appname)) : $corpname; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 25%"></td>
                <td>
                    <b>Address:</b> <?php echo $app->address; ?>
                </td>
            </tr>
        </table>
        <p>
            By signing on this Lease to Own Agreement (&#8220;<b>Agreement</b>&#8221;), the LESSEE hereby agrees to be bound by
            the following terms and conditions for the lease of the Solar Equipment:
        </p>
        <table class="bordered">
            <tr>
                <td class="leading">Solar Equipment</td>
                <td>Specifications for the Solar Equipment is provided in Annex &#8220;A&#8221;, which shall be considered an integral part of this Agreement.</td>
            </tr>
            <tr>
                <td class="leading">
                    Lease Term<br>(non-renewable)
                </td>
                <td>
                    <?php echo $planyear[$plan->years]; ?> years from Installation Date.
                </td>
            </tr>
            <tr>
                <td class="leading">
                    Monthly Rental Fee<br>(inclusive of value-added tax)
                </td>
                <td>
                    PHP <?php echo number_format($plan->monthlyamt,2); ?>
                </td>
            </tr>
            <tr>
                <td class="leading">
                    Installation Premises
                </td>
                <td>
                    <?php echo $app->address; ?>
                </td>
            </tr>
            <tr>
                <td class="leading">
                    Installation Date
                </td>
                <td>
                    <?php
                    if (isset($date)) {
                        echo $date;
                    } else {
                        echo $date = date('F j, Y');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="leading">
                    System Size
                </td>
                <td>
                    <?php echo substr($app->systemsizename,0,strpos($app->systemsizename,' ')); ?>
                    On-Grid No Batteries Included
                </td>
            </tr>
            <tr>
                <td class="leading">
                    Factory Warranty of Solar
                    Panels (80% Efficiency Rate)
                </td>
                <td>
                    <?php
                    $enddate = date_create_from_format('F j, Y',$date)->modify('+25 years');
                    echo $date . ' &#8211; ' . $enddate->format('F j, Y');
                    ?>
                </td>
            </tr>
        </table>
        <ol>
            <li>
                <b>Use of Solar Equipment.</b> The Solar Equipment shall be connected to the grid and shall be used as a
                means to generate electrical power from the sunlight using photovoltaic technology to supply the energy
                requirements of LESSEE. For this purpose, it is understood that LESSEE shall separately enter into a
                net-metering agreement with a distribution utility providing for an arrangement wherein LESSEE as a
                customer of the distribution utility, will generate electricity for his/her/its own use.
            </li>
            <li>
                <b>Installation.</b> LESSEE represents and warrants that it has the legal right to possess the Installation
                Premises and use it for the purpose contemplated herein. The installation of the Solar Equipment shall
                be performed by LESSOR at the Installation Premises. The Solar Equipment shall not be moved by
                LESSEE to a different site, without the prior written consent of LESSOR, during the Lease Term.
                LESSEE shall likewise not make any alterations or repairs to the Installation Premises which could
                adversely affect the operation and maintenance of the Solar Equipment without the LESSOR&#8217s written
                consent. Notwithstanding anything to the contrary, LESSEE shall be responsible for all damage to the
                Solar Equipment caused by the alteration or repair in the Installation Premises. LESSEE shall be
                responsible for using commercially reasonable efforts to maintain the security of the Solar Equipment
                against known risks and risks that should have been known by LESSEE. LESSEE will not conduct
                activities on, in or about the Installation Premises or the Solar Equipment that have a reasonable
                likelihood of causing damage, impairment or otherwise adversely affecting the Solar Equipment.
                LESSEE further represents and warrants that there are no hazardous substances at, on, above, below
                or near the Installation Premises.
            </li>
            <li>
                <b>Ownership, Operations and Maintenance.</b> The Solar Equipment shall be owned by LESSOR until
                the execution of the Deed of Absolute Sale by the parties under Clause 9 hereof; however, LESSEE
                shall be responsible for the operation and maintenance of the Solar Equipment in a reasonably
                competent manner, and shall comply with the requirements of the rules of use and application for the
                Solar Equipment.
            </li>
            <li>
                <b>Repairs and Replacement.</b> In the event the Solar Equipment requires any repairs, LESSEE shall
                promptly notify LESSOR of such necessity. LESSOR shall, within a reasonable period after the notice,
                effect the necessary repairs or replacement, as the case maybe, at the cost of LESSOR, except for
                loss, defect or damage attributable to the LESSEE, regardless of the existence of any fault or
                negligence on the part of the latter; in which case, LESSOR shall be entitled to recover the cost of
                repairs and/or replacement from LESSEE and collect a penalty fee of the cost of the assessed item
                damaged.
            </li>
            <?php if ($plan->years == 10) { ?>
                <li>
                    <b>Insurance.</b> All property insurance for loss and/or damage to the Solar Equipment due to acts of God
                    shall be the responsibility of LESSOR. LESSOR represents that it maintains and covenants that it shall
                    maintain during the term of this Agreement property insurance sufficient to insure it against complete
                    loss or destruction of the Solar Equipment due to acts of God. LESSEE shall reasonably cooperate
                    with the insurance company in case of any claim initiated on the insurance policy and shall refrain from
                    doing any act which will cause such claim to be denied.                    </li>
            <?php } ?>
            <li>
                <b>Access Rights.</b> During the Lease Term, LESSEE grants to LESSOR and to LESSOR&#8217s agents,
                employees, contractors and assignees an irrevocable non-exclusive access to, on, over, and across
                the Installation Premises for the purpose of (i) installing, constructing, accessing, removing and
                replacing the Solar Equipment, (ii) performing all of LESSOR&#8217s obligations and enforcing all of
                LESSOR&#8217s rights under this Agreement, and (iii) installing, using, and maintaining electric lines and
                equipment, including inverters and meters necessary to interconnect the Solar Equipment to electricity
                power grid, or for any purpose that may from time to time be useful or necessary in connection with the
                construction, installation, replacement or repair of the Solar Equipment.
            </li>
            <li>
                <b>Indemnity.</b> LESSEE agrees to defend, indemnify and hold LESSOR harmless against all liabilities,
                damages, losses, expenses and claims of any nature whatsoever from personal injury and for damage
                to or loss of any property arising out of or in any way connected with the construction, installation,
                replacement or repair of the Solar Equipment on the Installation Premises.
            </li>
            <li>
                <b>Rent.</b> LESSEE shall pay LESSOR the Monthly Rent within the <?php echo $monthly; ?> day of every month commencing
                on Execution Date (<?php echo $execdate;?>). If Monthly Rent is not paid on or before Due Date, any outstanding
                amount shall accrue interest at the annual rate of eight percent (8%) until full and complete payment.
                In addition, LESSOR shall have the right to suspend the performance of its obligations under this
                Agreement until LESSEE&#8217s full and complete payment of all outstanding amount due and payable to
                LESSOR.
            </li>
            <li>
                <b>Taxes.</b> Monthly Rental Fees are inclusive of all taxes and fees.
            </li>
            <li>
                <b>Execution of Sale.</b> Subject to LESSEE&#8217s compliance with all its obligations to the satisfaction of the
                LESSOR, including LESSEE&#8217s full and complete payment of all outstanding fees and payables to
                LESSOR prior to the end of the Lease Term, LESSOR agrees to execute a Deed of Absolute Sale in
                favor of LESSEE, thereby conveying all its rights, interests, and title over the Solar Equipment unto the
                latter, within thirty (30) days following the end of the Lease Term.
            </li>
            <li>
                <b>Inverter Replacement.</b> Notwithstanding anything to the contrary, the LESSEE agrees that it shall be
                responsible for the replacement and repair at his own cost of the power inverters every five (5) years
                or as necessary. LESSOR shall not, in any way, be held liable for any damage or liability accruing from
                LESSEE&#8217s failure to timely replace the power inverter.
            </li>
            <li>
                <b>Limitation of Liability.</b> To the maximum extent permitted under applicable law, LESSOR&#8217s entire
                liability to LESSEE arising out of or in any way related to this Agreement shall, in no event, exceed the
                aggregate amount of Rental Fees paid by LESSEE to LESSOR. Notwithstanding anything to the
                contrary, LESSOR shall not be liable for any indirect, special, incidental, consequential or exemplary
                damages, whether foreseeable or not, that are in an way related to this Agreement, the breach thereof,
                the use or inability to use the Solar Equipment, the results generated from the use of the Solar
                Equipment, the quality of the Solar Equipment, any defect in the Solar Equipment, failure of the Solar
                Equipment to perform as represented or expected, any transactions resulting from this Agreement, loss
                of goodwill or profits, lost business however characterized and/or from any other cause whatsoever.
            </li>
            <?php if ($plan->years == 10) { ?>
                <li>
                    <b>Degree of diligence required.</b> The diligence required in this <b><i>Agreement</i></b> is <b>ordinary diligence.</b>
                    Pursuant to Article 1173 of the New Civil Code, where it is not stipulated in the law or the contract, the
                    diligence required to comply with one's obligations is commonly referred to as paterfamilias; or, more
                    specifically, as bonus paterfamilias or "a good father of a family." A good father of a family means a
                    person of ordinary or average diligence. To determine the prudence and diligence that must be required
                    of all persons, parties herein agree to use as basis the abstract average standard corresponding to a
                    normal orderly person. Either party who uses diligence below this standard is guilty of negligence.
                </li>
            <?php } ?>
            <li>
                <b>Disclaimer.</b> Except as specifically provided to the contrary in this Agreement, neither Party makes any
                representations or extends any warranties of any kind, either express or implied, including, but not
                limited to, warranties of merchantability, fitness for a particular purpose, non-infringement or validity of
                any patents issued or pending.
            </li>
            <li class="nested">
                <b>Force Majeure.</b> If either Party is unable to carry out its obligations under this Agreement due to force
                majeure, the Parties agree to suspend performance until the event creating the force majeure is over.
                Neither party shall be liable for any loss or damage by reason of such failure or delay in performance
                caused by the force majeure. Notwithstanding anything to the contrary, LESSOR reserves and
                maintains its right to pull-out the Solar Equipment without incurring any liability under this Agreement
                for any occurrence or ground which in its sole discretion, renders the lease of the Solar Equipment no
                longer commercially viable such as but not limited to the following instances: a) government regulation,
                prohibition or restriction on the use of Solar Equipment; b) breach by LESSEE of any of its
                representation or warranties hereunder; c) total destruction of the Solar Equipment by reason of
                fortuitous event; or d) Event of Default on the part of LESSEE under Section 17 of this Agreement,
                without prejudice to the liability of LESSEE to pay any rental or charges accruing until the actual removal
                of the Solar Equipment from the Installation Premises.

                <ol>
                    <li>
                        <b>Automatic Revocation Clause.</b> The parties stipulate that they waive the
                        requirement of any judicial action to affect rescission pursuant to the 3rd paragraph of Article 1191
                        of the New Civil Code and in turn agree that the substantial breach by one party shall entitle the
                        other to extra-judicially rescind this Agreement by sending a written notice to the other. The
                        rescission shall take effect immediately upon receipt by the addressee of the written notice.
                    </li>
                    <li>
                        <b>Proof of receipt of notice.</b> Service of the written notice may be done by personal
                        service, registered mail with return card, or through private courier. The same may be established
                        by the signature of the addressee or his employee or any representative who is of legal age,
                        stationed on the address provided herein. But any unjustified refusal to sign or acknowledge receipt
                        of the written notice or the return card in case of service by mail or private courier, may be proved
                        by an Affidavit of Service by the person who attempted to serve the same or the postal service
                        representative.
                    </li>
                </ol>
            </li>
            <li>
                <b>Event of Default.</b> Any of the following occurrences shall be considered an Event of Default, which shall
                entitle the non-Defaulting Party to terminate upon written notice to the Defaulting Party: (i) failure of
                LESSEE to pay any amount due and payable under this Agreement, other than an amount that is
                disputed by LESSEE in writing, within ten (10) days following receipt of written notice from LESSOR of
                such failure to pay; (ii) any representation or warranty of LESSEE proves at any time to have been
                incorrect in any material respect; (iii) LESSEE loses its rights to occupy and enjoy the Installation
                Premises; (iv) a Party becomes insolvent or is a party to a bankruptcy, reorganization, insolvency,
                liquidation, receivership, dissolution, winding-up or relief of debtors, or any general assignment for the
                benefit of creditors or other similar arrangement or any event occurs or proceedings are taken in any
                jurisdiction with respect to the Party which has a similar effect; and (v) LESSEE prevents LESSOR from
                performing any of LESSOR's obligations under this Agreement, including the installation of the Solar
                Equipment and prevents LESSOR from accessing the Installation Premises. Notwithstanding anything
                to the contrary, the Event of Default under this Clause 14(v) shall not excuse LESSEE's obligations to3
                make payments that otherwise would have been due under this Agreement. Pre-termination of this
                Agreement, for any cause whatsoever, shall not entitle the LESSEE to a refund of any Monthly Fees
                already paid or accrued, which are automatically deemed forfeited in favor of LESSOR. A termination
                will apply ONE (1) year from date of installation, also additional fee will be computed and will be charged
                to the LESSEE.
            </li>
            <li>
                <b>Sale of Property.</b> LESSEE shall notify LESSOR in writing prior to the sale or execution of transfer
                documents, in the event LESSEE decides to sell the property wherein the Installation Premises is
                situated or enter into any agreement which will make LESSEE lose the right of possession over the
                Installation Premises, at any time during the Lease Term. In such case, LESSOR shall be entitled to
                pre-terminate this Agreement and collect from LESSEE all Monthly Rental Fees corresponding to the
                unexpired portion of the Lease Term.
            </li>
            <li>
                <b>Early Buyout.</b> If LESSEE decides to buyout the Solar Equipment within three (3) months after the
                installation, no interest will be charged and LESSEE will pay the price of the Solar Equipment outright
                price. For the buyout after three (3) months from the installation date, an interest will be computed and
                added to the outright price.
            </li>
            <li>
                <b>Representations and Warranties.</b> The Parties represent and warrant in favor of each other that:
                <ol type="a" class="letterlist">
                    <li>
                        Each Party has full authority, and legal right to execute, deliver, and perform all acts under this Agreement.
                    </li>
                    <li>
                        This Agreement constitutes the legal, valid, and binding obligation, enforceable in accordance
                        with the terms hereof.
                    </li>
                    <li>
                        The execution, delivery, and performance of this Agreement does not and will not violate any
                        provision of, or result in a breach of, or constitute a default under any law, regulation, or
                        judgment, or violate any agreement binding upon either of the Parties or of any of their
                        properties.
                    </li>
                </ol>
            </li>
            <?php if ($plan->years == 10) { ?>
                <li>
                    <b>Qualification.</b> LESSEE further represents and warrants that he/she has been pre-qualified in writing
                    by LESSOR to enter into this Agreement and has shown a sufficient financial capacity to purchase the
                    Solar Equipment in the form of Monthly Rent, as shown by his/her submission of the pre-qualification
                    documents set forth under <b>Annex &#8220;B&#8221;</b> hereof.
                </li>
            <?php } ?>
            <li>
                <b>Data Privacy.</b> LESSEE consents to the manual and electronic holding and processing by LESSOR of
                any data collected from the LESSEE or the Solar Equipment or from any software application connected
                thereto for the purpose of determining the condition of the Solar Equipment and other purposes
                necessary or desirable to carry out the provisions of this Agreement. LESSOR hereby warrants that it
                is observing the appropriate level of data privacy protection in compliance with the standards prescribed
                under the Data Privacy Law.
            </li>
            <li>
                <b>Assignment.</b> This Agreement is not assignable, directly or indirectly, by LESSEE.
            </li>
            <li>
                <b>Notices.</b> All notices, including notices of address change, required to be sent hereunder shall be in
                writing and shall be deemed to have been given when delivered by hand or by registered mail to the
                address provided above.
            </li>
            <li>
                <b>Settlement of Dispute.</b> The Parties agree to use reasonable efforts to resolve any dispute or
                disagreements concerning the interpretation or implementation of this Agreement through mutual
                consultation or negotiation. Should no resolution be reached after thirty (30) days from the date of the
                first consultation or negotiation, the Parties agree to submit to the jurisdiction of the proper courts in
                Iloilo City Court, to the exclusion of any other courts.
            </li>
            <li>
                <b>Separability.</b> If any provision contained herein is invalid, illegal or unenforceable in any respect under
                any applicable law or decision, the validity, legality and enforceability of the remaining provisions shall
                not be affected in any way. The Parties shall so far as practicable, execute such additional documents,
                or shall perform any other action not otherwise contrary to the terms of this Agreement, in order to give
                effect to any provision hereof which is determined to be invalid, illegal or unenforceable.
            </li>
            <li>
                <b>Governing Law.</b> This Agreement shall be interpreted and construed in accordance with the laws of the
                Philippines.
            </li>
        </ol>
        <div class="page_break">
            <p style="text-indent: 30px">
                <b>IN WITNESS WHEREOF</b>, the Parties hereto have hereunto signed this Lease to Own Agreement
                on the date first above written at Iloilo City.
            </p>
            <br>
            <br>
            <div style="width: 50%; display: inline-block; text-align: center">
                <b>PANAY ALTERNATIVE ENERGY INC.</b>
                <br>
                LESSOR
                <br><br>
                By:
                <br><br><br>
                Marcelo U. Cacho
                <br>
                <b>General Manager</b>
            </div>
            <div style="width: 50%; display: inline-block; text-align: center">
                <b>LESSEE</b>
            </div>
            <br>
            <!--<div style="display: block; text-align: center;">
                    SIGNED IN THE PRESENCE OF:
            </div>
            <p><br></p>
            <div style="display: inline-block; width: 49%; text-align: center">
                __________________________
            </div>
            <div style="display: inline-block; width: 49%; text-align: center">
                __________________________
            </div>
            </div>
            <div class="page_break">
            <p class="center">ACKNOWLEDGMENT</p>
            <br>
            <table style="width: 45%">
                <tr>
                    <td style="width: 38.5%; vertical-align: top">REPUBLIC OF THE PHILIPPINES</td>
                    <td>)<br>) S.S.</td>
                </tr>
            </table>
            <p class="paragraph">
                BEFORE ME, a Notary Public for and in Iloilo City, Philippines, personally appeared:
            </p>
            <table class="bordered" width="90%">
                <tr>
                    <td class="center" style="width: 33.33%;">NAME</td>
                    <td class="center" style="width: 33.33%;">GOVT. ID NO</td>
                    <td class="center" style="width: 33.33%;">DATE/PLACE ISSUED</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Marcelo U. Cacho</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
            <p>
                known to me and to me known to be the same persons who executed the foregoing instrument, and
                acknowledged that they executed the same as their free act and deed.
            </p>
            <p class="paragraph">
                This instrument is consisting of _______ (__) pages, including this page where the
                acknowledgment is written, has been signed by the parties and witnesses on each and every page thereof.
            </p>
            <p class="paragraph">
                WITNESS MY HAND AND SEAL, this ___ day of _____________ in the place first above
                mentioned.
            </p>
            <br>
            <br>
            <br>
            <p>
                Doc. No. _____;<br>
                Page No. _____;<br>
                Book No._____;<br>
                Series of 20___.<br>
            </p>
            </div>-->
        </body>
        </html>
        <?php
    } else {
        //outright document layout.
    }
} else {
    echo '<h1>Cannot find Customer Plan Details.</h1>';
    echo '<h4>Kindly set it before refreshing document preview.</h4>';
}