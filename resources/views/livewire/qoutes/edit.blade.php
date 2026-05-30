<div>
    <div>
        @include('includes.flash')
        <form class="border-2" wire:submit.prevent="save" onkeydown="if(event.key === 'Enter') event.preventDefault();">
            <input type="hidden" name="specialreqval" wire:model="sp_reqs">

            <table width="100%" class="border-primary" border="1" cellpadding="1" cellspacing="1" bordercolor="#e6e6e6"
                class="up" style="left:35px;">
                <tr>
                    <td height="25" bgcolor='#336699' colspan="3" align="center">
                        <font color="white"><span class="style1">Online Request For Quote Form</font></span>
                    </td>
                </tr>

                <tr>
                    <td height="25" class="p-2" colspan='3'><strong>Lookup ID :</strong>
                        <input type="text" size="60" wire:model="search" wire:keyup="onKeyUp($event.target.value)"
                            autocomplete="off" />
                        @if($matches)
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            @foreach($matches as $i => $m)
                            <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                wire:click="useMatch({{ $i }})">
                                {{ $m['label'] }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td width="312" class="p-2" height="25"><strong>Customer :</strong>
                         <select wire:model="cust_name" id="cust_name" wire:change="changecustomer" wire:key="cust_name-{{ $inputKey }}">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->c_name }}" @selected($cust_name==$customer->c_name)>
                                {{ $customer->c_name }}
                            </option>
                            @endforeach
                        </select> <br />
                        @error('cust_name')
                        <font color="red"><small>{{ $message }}</small></font>
                        @enderror
                    </td>

                    <td width="252"><strong>Part Number :</strong>
                        <input type="text" name="txtpno" wire:model="part_no" wire:key="part-{{ $inputKey }}" />
                        @error('part_no')
                        <font color="red"><small>{{ $message }}</small></font>
                        @enderror
                    </td>

                    <td width="238"><strong>Rev :</strong>
                        <input name="txtrev" wire:model="rev" wire:key="rev-{{ $inputKey }}" size="2" />
                        <label for="new"><strong> New</strong></label>
                        <input type="radio" name="nor1" wire:model.live="new_or_rep" value="New Part" id="new" />
                        &nbsp;&nbsp;&nbsp;
                        <label for="rep"><strong>Repeat</strong></label>
                        <input type="radio" name="nor1" wire:model.live="new_or_rep" value="Repeat Order" id="rep" />
                    </td>
                </tr>

                <tr>
                    <td height="25" class="p-2"><strong>Requested By :</strong>
                        <select class="w-50" wire:change="requestby" wire:model="request_by">
                            <option>Select Requested By</option>
                                                            @foreach($customers_main as $main)
                                    <option 
                                        value="m**{{ $main->enggcont_id }}**{{ $main->name }}"
                                        @if($request_by == 'm**'.$main->id.'**'.$main->name) selected @endif>
                                        
                                        {{ $main->name }}
                                    </option>
                                @endforeach

                                @foreach($customers_eng as $eng)
                                    <option 
                                        value="e**{{ $eng->enggcont_id }}**{{ $eng->name }}"
                                        @if($request_by == 'e**'.$eng->id.'**'.$eng->name) selected @endif>
                                        
                                        {{ $eng->name }}
                                    </option>
                                @endforeach
                        </select>
                    </td>

                    <td colspan="2" class="p-2" id="content2" height="25">
                        <strong>Email : </strong>
                        <input type="text" name="txtemail" wire:key="email-{{ $inputKey }}" wire:model="email" />
                        <strong>Phone :</strong>
                        <input type="text" name="txtphone" wire:key="phone-{{ $inputKey }}" wire:model="phone" />
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="p-2">
                        <strong>FAX :</strong>
                        <input name="txtfax" wire:model="fax" size="14" />

                        <strong>Quote Needed by: </strong>
                        <input name="txtquote" wire:model="quote_by" size="10" />
                        <strong> NRE Charge:</strong>
                        <input size="3" type="text" name="necharge" wire:model="necharge" @if($new_or_rep == "Repeat Order") disabled @endif>
                        Select Misc :
                        <select name="txtmisc" wire:model="selectedMisc" onchange="getmisc();"
                            wire:change="showMiscField">
                            <option value="">--select MISC--</option>
                            <option value="m1">Misc 1</option>
                            <option value="m2">Misc 2</option>
                            <option value="m3">Misc 3</option>
                        </select>
                        &nbsp;
                        <input type="checkbox" id="simplequote" name="simplequote" wire:model.live="simplequote" />
                        Simple Quote
                        <br />

                        <table width='100%' border='0'>
                            <tr>
                                <td>
                                    @if($showMisc1)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    @endif

                                    @if($showMisc2)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    <div id="misc2">
                                        <strong>Misc Charge2:</strong>
                                        <input size="3" type="text" name="descharge1" wire:model="descharge1">
                                        &nbsp;Name of Misc2 :
                                        <input type="text" name="desdesc1" wire:model="desdesc1" />
                                    </div>
                                    @endif

                                    @if($showMisc3)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    <div id="misc2">
                                        <strong>Misc Charge2:</strong>
                                        <input size="3" type="text" name="descharge1" wire:model="descharge1">
                                        &nbsp;Name of Misc2 :
                                        <input type="text" name="desdesc1" wire:model="desdesc1" />
                                    </div>
                                    <div id="misc3">
                                        <strong>Misc Charge3:</strong>
                                        <input size="3" type="text" name="descharge2" wire:model="descharge2">
                                        &nbsp;Name of Misc3 :
                                        <input type="text" name="desdesc2" wire:model="desdesc2" />
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div id='comments' @if($simplequote==false) style="visibility:hidden;" @endif>
                                        <br><b>Comments:</b><br>
                                        <textarea name="comments" wire:model="comments" rows="3" cols="30"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @if($simplequote == false)
            <div id='complexform'>
                <table width="100%" class="border-primary" border="1" cellpadding="1" cellspacing="1"
                    bordercolor="#e6e6e6" class="up" style="left:35px;">
                    <tr>
                        <td class="p-2">
                            <strong><br />Cancellation</strong>
                            <input type="radio" name="cancharge" wire:model="cancharge" value="yes"> Yes
                            <input type="radio" name="cancharge" wire:model="cancharge" value="no"> No
                            <strong>Charge</strong>
                            <input name="ccharge" wire:model="ccharge" size="8" />

                            <strong>FOB:</strong>
                            <select name="fob" wire:model="fob">
                                <option value="Anaheim">Anaheim</option>
                                <option value="Customer Dock">Customer Dock</option>
                                <option value="Factory">Factory</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Other">Other</option>
                            </select>
                            @if($fob === 'Other')
                            <div id="fob_oth">Other: <input type="text" name="fob_oth" wire:model="fob_oth" size="15"
                                    maxlength='50'></div>
                            @endif

                            <br><br><strong>Vendor:</strong>
                            <select name="vid" wire:model="vid">
                                <option value=''>Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                                @endforeach
                                <option value='9999'>Other</option>
                            </select>
                            @if($vid === '9999')
                            <div id="vid_oth">Other: <input type="text" name="vid_oth" wire:model="vid_oth" size="30"
                                    maxlength='100'></div>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Qty(s) : </strong>
                            <!-- Quantities -->
                            @foreach(range(1, 10) as $i)
                            <input type="text" wire:model="quantities.{{ $i }}" size="3" class="mr-2">
                            @endforeach

                            <!-- Days --> <br /> <br />
                            <strong>Days(s) : </strong>
                            @foreach(range(1, 5) as $i)
                            <input type="text" wire:model="days.{{ $i }}" size="3" class="mr-2">
                            @endforeach

                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">

                            <!-- Manual Price Button -->
                            <button type="button" wire:click="toggleManualPrice" class="btn btn-primary mb-4">
                                Manual Price
                            </button>

                            <!-- Manual Price Inputs Table -->
                            @if($manualMode)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Qty \ Days</th>
                                            @foreach(range(1, 20) as $dayIndex)
                                            @if(!empty($days[$dayIndex]))
                                            <th>{{ $days[$dayIndex] }} Days</th>
                                            @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(range(1, 20) as $qtyIndex)
                                        @if(!empty($quantities[$qtyIndex]))
                                        <tr>
                                            <td>{{ $quantities[$qtyIndex] }} pieces</td>
                                            @foreach(range(1, 20) as $dayIndex)
                                            @if(!empty($days[$dayIndex]))
                                            <td>
                                                <input type="text"
                                                    wire:model="manualPrices.{{ $qtyIndex }}.{{ $dayIndex }}"
                                                    class="form-control form-control-sm">
                                            </td>
                                            @endif
                                            @endforeach
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width='100%'>
                                <tr>
                                    <td colspan='2' height="25" bgcolor='#336699' align="Left">
                                        <font color="white"><strong id='labelcomments'>ADMIN INSTRUCTIONS:</strong>
                                        </font>
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td height="25" width='50%'>
                                                                     <!-- SimpleMDE CDN -->
                        
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

<style>
    .CodeMirror {
        height: 120px !important;
        min-height: 100px;
    }
</style>

<div wire:ignore>
    <input type="hidden" wire:model="special_instadmin" id="adminContent">
    <textarea id="txtinstadmin" name="txtinstadmin"></textarea>
</div>

<script>
    let simplemde = null;
    
    function initSimpleMDE() {
        if (simplemde) {
            simplemde.toTextArea();
            simplemde = null;
        }
        
        simplemde = new SimpleMDE({
            element: document.getElementById('txtinstadmin'),
            spellChecker: false,
            toolbar: ['bold', 'italic', 'unordered-list', 'ordered-list'],
            status: false,
            lineWrapping: true,
        });
        
        // Load existing content - Convert <br /> to \n for display
        const existingContent = @json($special_instadmin);
        if (existingContent) {
            let displayContent = existingContent.replace(/<br\s*\/?>/gi, '\n');
            simplemde.value(displayContent);
        }
        
        // On change: Convert \n to <br /> for backend
        simplemde.codemirror.on('change', function() {
            let rawContent = simplemde.value();
            // Convert newlines to <br /> (self-closing tag)
            let contentWithBr = rawContent.replace(/\n/g, '<br />');
            document.getElementById('adminContent').value = contentWithBr;
            @this.set('special_instadmin', contentWithBr);
        });
    }
    
    document.addEventListener('livewire:init', function() {
        initSimpleMDE();
    });
    
    document.addEventListener('livewire:navigated', function() {
        initSimpleMDE();
    });
</script>
                                    </td>

                                    <td height="25">
                                        <strong>Replace with Comments</strong> <br>
                                        Yes
                                        <input name="admcmntstat" type="radio" wire:model="is_spinsadmact"
                                            value="yes" />
                                        <br>
                                        No &nbsp;<input name="admcmntstat" type="radio" wire:model="is_spinsadmact"
                                            checked value="no" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="upTT" width="100%" border="1" style="border-color: #336699; left:35px;" cellpadding="1"
                    cellspacing="0" bordercolor="#e6e6e6">
                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Number of Layers : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Material Required : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>IPC Class:</strong> 1
                                <input name="chki1" type="radio" wire:model="ipc_class" value="1" /> 2
                                <input name="chki1" type="radio" wire:model="ipc_class" value="2" /> 3
                                <input name="chki1" type="radio" wire:model="ipc_class" value="3" />
                            </font>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            Single Sided <input name="chk1" type="radio" wire:model="no_layer" value="single" />
                            Double Sided <input name="chk1" type="radio" wire:model="no_layer" value="Double Sided" />
                            <br /><br />
                            <strong>Multilayer: </strong>4Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="4Lyrs" /> 6Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="6Lyrs" /> 8Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="8Lyrs" /> 10Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="10Lyrs" />
                            <br /><br />
                            Other  :
                            <input name="chk1" type="radio" value="Other" wire:model="chk1"  />
                            <input type="text" name="txtother1" wire:model="txtother1" />
                        </td>

                        <td height="25" class="p-2">
                            FR-4 <input name="chk7" type="radio" wire:model="m_require" value="FR-4"
                                onclick="clearval('txtother2')" />
                            &nbsp;
                            FR-4/170TG Min <input name="chk7" type="radio" wire:model="m_require" value="FR-4/170TG Min"
                                onclick="clearval('txtother2')" />
                            <br /><br />
                            Rogers 4003 <input name="chk7" type="radio" wire:model="m_require" value="Rogers 4003"
                                onclick="clearval('txtother2')" />
                            &nbsp;
                            Other: <input name="chk7" type="radio" wire:model="chk7" value="Other" />
                            <input name="txtother2" wire:model="txtother2" type="text" size="12" />
                            <br /><br />

                            <strong>Inner Copper Oz: </strong>N/A
                            <input name="chk18" type="radio" wire:model="inner_copper" value="N/A" />
                            &nbsp; .5
                            <input name="chk18" type="radio" wire:model="inner_copper" value=".5" />
                            &nbsp; 1
                            <input name="chk18" type="radio" wire:model="inner_copper" value="1" />
                            &nbsp; 2
                            <input name="chk18" type="radio" wire:model="inner_copper" value="2" />
                            <br />
                            Other <input name="chk18" type="radio" wire:model="chk18" value="Other" />
                            <input name="txtother6" wire:model="txtother6" type="text" size="10" /> Oz
                        </td>

                        <td height="25" valign="top" class="p-2">
                            <strong>Thickness:</strong> 0.031&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.031"
                                onclick="clearval('txtother4')" />
                            &nbsp; 0.062&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.062"
                                onclick="clearval('txtother4')" />
                            <br /><br />
                            0.093&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.093"
                                onclick="clearval('txtother4')" />
                            &nbsp; Other:
                            <input name="chk13" type="radio" wire:model="thickness" value="Other" />
                            <input name="txtother4" wire:model="txtother4" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Thickness Tolerance:</strong>
                            <br />
                            <br />
                            +-10% <input name="chk17" type="radio" wire:model="thickness_tole" value="+/- 10%" />
                            Other <input name="chk17" type="radio" wire:model="chk17" value="Other" />
                            <input name="txtother5" wire:model="txtother5" type="text" size="5" />
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Plate : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Design Type/Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Design Technology : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" width="307px" class="p-2">
                            <strong>External Cu Oz:</strong> 0.5
                            <input name="chk10" type="radio" wire:model="start_cu" value="0.5" />
                            &nbsp; 1
                            <input name="chk10" type="radio" wire:model="start_cu" value="1" />
                            &nbsp; 2
                            <input name="chk10" type="radio" wire:model="start_cu" value="2" />
                            <br />
                            <br />
                            Other :
                            <input name="chk10" type="radio" wire:model="chk10" value="Other" />
                            <input type="text" name="txtother3" wire:model="txtother3" /> Oz
                            <br />
                            <br />
                            <br />
                            <strong>Plated Cu in Holes (Min.):</strong>
                            .0010 <input name="chk22" type="radio" wire:model="plated_cu" value=".0010" />
                            &nbsp; .0014 <input name="chk22" type="radio" wire:model="plated_cu" value=".0014 " />
                            &nbsp;&nbsp;&nbsp;&nbsp;1oz <input name="chk22" type="radio" wire:model="plated_cu"
                                value="1oz " />
                            &nbsp; Other <input name="chk22" type="radio" wire:model="chk22" value="Other" />
                            <input name="txtother7" wire:model="txtother7" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Fingers Nickel/Hard Gold: </strong>
                            Yes <input name="chk25" type="checkbox" wire:model="fingers_gold" value="yes" />
                        </td>

                        <td height="25" width="330px" class="p-2" valign="top">
                            <strong>Trace Min. = </strong>.006
                            <input name="chk27" type="radio" wire:model="trace_min" value=".006"
                                onclick="clearval('txtother54')" />
                            .005 <input name="chk27" type="radio" wire:model="trace_min" value=".005"
                                onclick="clearval('txtother54')" />
                            .004 <input name="chk27" type="radio" wire:model="trace_min" value=".004"
                                onclick="clearval('txtother54')" />
                            .003 <input name="chk27" type="radio" wire:model="trace_min" value=".003"
                                onclick="clearval('txtother54')" />
                            <br />Other <input name="chk27" type="radio" wire:model="trace_min" value="Other" />
                            <input name="txtother54" wire:model="txtother54" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Space Min. =</strong>.006
                            <input name="chk31" type="radio" wire:model="space_min" value=".006"
                                onclick="clearval('txtother55')" />
                            .005 <input name="chk31" type="radio" wire:model="space_min" value=".005"
                                onclick="clearval('txtother55')" />
                            .004 <input name="chk31" type="radio" wire:model="space_min" value=".004"
                                onclick="clearval('txtother55')" />
                            .003 <input name="chk31" type="radio" wire:model="space_min" value=".003"
                                onclick="clearval('txtother55')" />
                            <br />
                            Other <input name="chk31" type="radio" wire:model="chk31" value="Other" />
                            <input name="txtother55" wire:model="txtother55" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Controlled Impedance:</strong>
                            <input name="chk35" type="checkbox" wire:model="con_impe_sing" value="Yes" /> Yes
                            <br />
                            <br />
                            Single Ended <input name="chk109" type="checkbox" wire:model="con_impe_sing"
                                value="Single Ended" />
                            Differential <input name="chk110" type="checkbox" wire:model="con_impe_diff"
                                value="Differential" />
                            <br />
                            <br />
                            <strong>Impedance Tolerance:</strong>
                            <br />+-10% <input name="chk202" type="radio" wire:model="tore_impe" value="+/- 10%" />
                            Other <input name="chk202" type="radio" wire:model="chk202" value="Other" />
                            <input name="txtother51" wire:model="txtother51" type="text" size="10" />
                        </td>

                        <td height="25" valign="top" class="p-2">
                            <strong>Smallest Hole Size:</strong>
                            <input name="txtother8" wire:model="hole_size" type="text" size="8" />
                            <br /><br />
                            <strong>Smallest Pad:</strong>
                            <input name="txtother19" wire:model="pad" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Blind Vias:</strong>
                            <input name="chk37" type="checkbox" wire:model="blind" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>Buried Vias: </strong>
                            <input name="chk39" type="checkbox" wire:model="buried" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>HDI Design:</strong>
                            <input name="chk200" type="checkbox" wire:model="hdi_design" value="Yes" /> Yes
                            <br />
                            <br />
                            <strong>Non-Conductive Filled/Plated Over:</strong><br>
                            <input name="chk215" type="checkbox" wire:model="resin_filled" value="Yes" /> Yes
                            <br />
                            <strong>Conductive Filled Vias:</strong>
                            <input name="chk210" type="checkbox" wire:model="cond_vias" value="Yes" /> Yes
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Finish : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Solder Resist : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Nomenclature : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Finish:</strong> HASL
                            <input name="chk43" type="radio" wire:model="finish" value="HASL" />
                            &nbsp; Lead-Free HASL
                            <input name="chk43" type="radio" wire:model="finish" value="Lead-Free HASL" />
                            <br />
                            <br />
                            ENIG <input name="chk43" type="radio" wire:model="finish" value="ENIG" />
                            &nbsp; Imm.Silver
                            <input name="chk43" type="radio" wire:model="finish" value="Imm.Silver" />
                            &nbsp; Imm.Tin
                            <input name="chk43" type="radio" wire:model="finish" value="Imm.Tin" />
                            <br />
                            <br />
                            Other : <input name="chk43" type="radio" wire:model="chk43" value="Other" />
                            <input name="txtother9" wire:model="txtother9" type="text" size="15" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Mask Sides:</strong> N/A
                            <input name="chk48" type="radio" wire:model="mask_size" value="N/A" />
                            &nbsp; 1
                            <input name="chk48" type="radio" wire:model="mask_size" value="1" />
                            &nbsp; Both
                            <input name="chk48" type="radio" wire:model="mask_size" value="Both" />
                            <br />
                            <br />
                            <strong>Color:</strong> Green
                            <input name="chk51" type="radio" wire:model="color" value="Green"
                                onclick="clearval('txtother10')" />
                            &nbsp; Blue
                            <input name="chk51" type="radio" wire:model="color" value="Blue"
                                onclick="clearval('txtother10')" />
                            <br />
                            <br />
                            Other : <input name="chk51" type="radio" wire:model="color" value="Other" />
                            <input name="txtother10" wire:model="txtother10" type="text" size="15" />
                            <br />
                            <br />
                            <strong>Mask Type:</strong> Glossy
                            <input name="chk53" type="radio" wire:model="mask_type" value="Glossy" />
                            &nbsp; Matte
                            <input name="chk53" type="radio" wire:model="mask_type" value="Matte " />
                        </td>

                        <td height="25" width="273px" class="p-2">
                            <strong>S/S Sides: </strong>N/A
                            <input name="chk55" type="radio" wire:model="ss_side" value="N/A" />
                            &nbsp; 1
                            <input name="chk55" type="radio" wire:model="ss_side" value="1" />
                            &nbsp; 2
                            <input name="chk55" type="radio" wire:model="ss_side" value="2" />
                            <br />
                            <br />
                            <strong>S/S Color: </strong>
                            White <input name="chk58" type="radio" wire:model="ss_color" value="White"
                                onclick="clearval('txtother11')" />
                            Black <input name="chk58" type="radio" wire:model="ss_color" value="Black"
                                onclick="clearval('txtother11')" />
                            Yellow <input name="chk58" type="radio" wire:model="ss_color" value="Yellow"
                                onclick="clearval('txtother11')" />
                            <br />
                            <br />
                            <strong>Other:</strong>
                            <input name="chk58" type="radio" wire:model="chk58" value="Other" />
                            <input name="txtother11" wire:model="txtother11" type="text" size="15" />
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Fabrication : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Array Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Special Call-Outs : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Board Size (in.)</strong>
                            <input name="txtother12" wire:model="txtother12" type="text" size="5" /> X
                            <input name="txtother13" wire:model="txtother13" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Array:</strong>
                            <input name="chk63" type="checkbox" wire:model="array" value="YES" /> Yes
                            <br /><br />

                            <div id="yes_box2" style="display: block; ">
                                <strong>Bds Per Array:</strong>
                                <input name="txtother26" wire:model="txtother26" type="text" size="4" />
                                <br /><br />
                                <strong>Array Size:</strong>
                                <input name="txtother15" wire:model="txtother15" type="text" size="5" /> X
                                <input name="txtother16" wire:model="txtother16" type="text" size="5" />
                            </div>
                            <br />
                            <strong>Rout Tolerance:</strong> +/-.005
                            <input name="chk204" type="radio" wire:model="route_tole" value="+/-.005"
                                onclick="clearval('txtother53')" />
                            Other <input name="chk204" type="radio" wire:model="chk204" value="Other" />
                            <input name="txtother52" wire:model="txtother52" type="text" size="2" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Array Design Provided:</strong>
                            <input name="chk65" type="checkbox" wire:model="array_design" value="Yes" /> Yes
                            <br /><br />
                            <strong>Factory to Design Array: </strong>
                            <input name="chk67" type="checkbox" wire:model="design_array" value="yes" /> Yes
                            <br /><br />
                            <strong>Array Type:</strong> Tab Route
                            <input name="chk69" type="checkbox" wire:model="array_type1" value="Tab Route" />
                            &nbsp; V Score
                            <input name="chk70" type="checkbox" wire:model="array_type2" value="V Score" />
                            <br /><br />
                            <strong>Array Requires: </strong>Tooling Holes
                            <input name="chk72" type="checkbox" wire:model="array_require1" value="Tooling Holes" />
                            <br /><br />
                            Fiducials <input name="chk73" type="checkbox" wire:model="array_require2"
                                value="Fiducials" />
                            &nbsp; Mousebites
                            <input name="chk74" type="checkbox" wire:model="array_require3" value="Mousebites" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Milling: </strong>
                            <input name="chk75" type="checkbox" wire:model="bevel" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>Countersink:</strong>
                            <input name="chk77" type="checkbox" wire:model="counter_sink" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>Control Depth:</strong>
                            <input name="chk79" type="checkbox" wire:model="cut_outs" value="Yes" /> Yes
                            <br />
                            <br />
                            <strong>Edge Plating:</strong>
                            <input name="chk81" type="checkbox" wire:model="slots" value="Yes" /> Yes
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Markings : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>QA Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Miscellaneous : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" width="307px" class="p-2">
                            <strong>Logo:</strong>
                            Factory <input name="chk83" type="radio" wire:model="logo" value="Factory"
                                onclick="clearval('txtother56')" />
                            Other <input name="chk83" type="radio" wire:model="logo" value="Other" />
                            <input name="txtother56" wire:model="txtother56" type="text" size="3" /> Logo
                            <br />
                            <br />
                            <strong>94V-0 Mark: </strong>
                            <input name="chk85" type="checkbox" wire:model="mark" value="Yes" /> Yes
                            <br />
                            <br />
                            <strong>Date Code Format:</strong>
                            WWYY <input name="chk87" type="radio" wire:model="date_code" value="WWYY" />
                            &nbsp; YYWW <input name="chk87" type="radio" wire:model="date_code" value="YYWW" />
                            &nbsp;
                            <strong>Other Marking: </strong>
                            <input name="chk87" type="radio" wire:model="chk87" value="Other Marking " />
                            <input name="txtother17" wire:model="txtother17" type="text" size="10" /> D/C Format
                        </td>

                        <td height="25" class="p-2">
                            <strong>Microsection Required:</strong>
                            <input name="chk90" type="checkbox" wire:model="micro_section" value="YES" /> Yes
                            <br />
                            <br />
                            <strong>Electrical Test Stamp: </strong>
                            <input name="chk92" type="checkbox" wire:model="test_stamp" value="Yes" /> Yes
                            <br />
                            <br />
                            In Board <input name="chk94" type="checkbox" wire:model="in_board" value="In Board" />&nbsp;
                            In Array Rail <input name="chk199" type="checkbox" wire:model="array_rail"
                                value="In Array Rail" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>X-Outs Allowed:</strong>
                            <input name="chk95" type="checkbox" wire:model="xouts" value="yes" onClick="xoutsnot();" />
                            Yes
                            <br />
                            <br />
                            <strong># of X-outs per Array:</strong>
                            <input name="txtother28" wire:model="txtother28" type="text" size="4" />
                            <br /><br />
                            <strong>RoHS Cert:</strong>
                            <input name="chk97" type="checkbox" wire:model="rosh_cert" value="Yes" /> Yes
                        </td>
                    </tr>
                </table>
            </div>
            @endif
            <table class="upTT" width="100%" border="1" style="border-color: #336699; left:35px;" cellpadding="1"
                cellspacing="0" bordercolor="#e6e6e6">
                <tr>
                    <td height="25" colspan="3">
                        <div id='specialreq' style='color: #000; font: 11px/25px Verdana;'></div>
                    </td>
                </tr>

                <tr>
                    <td height="25" colspan="3" class="p-3">
                        <input type="button" class="btn btn-sm btn-primary @if($button_status == 1) disabled @endif" wire:click="save" value="Submit">
                        &nbsp;
                        <label><input type="reset" class="btn btn-sm btn-warning" name="button2"
                                value="Reset" /></label>
                        &nbsp;&nbsp;Receive reminders
                        <input type="checkbox" wire:model="reminders"> after every
                        <input type="text" name="days" wire:model="day" size='2' maxlength='3' value="15"> days
                    </td>
                </tr>

                <tr>
                    <td height="25" colspan="3">&nbsp;</td>
                </tr>
            </table>
        </form>
    </div>
       <!-- Alert Modal -->
    <div class="modal fade @if($showAlertPopup) show d-block @endif" id="alertModal" tabindex="-1"
        style="@if($showAlertPopup) display: block; @endif" role="dialog">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 500px;">
            <div class="modal-content" style="background: #ccffff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
                    style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                    <label class="modal-title fw-bold text-dark m-0" style="font-size: 18px;">
                        <i class="fa fa-bell"></i> Part no Alerts</label>
                    <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                    onclick="event.stopPropagation(); @this.call('closeAlertPopup');">Close</button>
                </div>

                <div class="modal-body pt-2 px-3">
                    @if(!empty($alertMessages))
                        @php 
                        $count = 1;
                        @endphp
                        @foreach($alertMessages as $index => $message)
                            <div class="pb-1 mb-1 border-bottom" wire:key="alert-{{ $message->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $count++ }}.</strong>
                                        <span style="font-size: 13px;">{{ $message->alert }}</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm text-primary" style="font-size: 12px;"
                                            wire:click="editAlert({{ $message->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm text-danger" style="font-size: 12px;"
                                            wire:click="deleteAlert({{ $message->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted mb-3" style="font-size: 13px;">No alerts found.</div>
                    @endif

                    <div class="mt-2 mb-2">
                        <label class="form-label small mb-1">
                            @if($editingAlertId)
                                Edit Alert
                            @else
                                Add New Alert
                            @endif
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{{ $newAlert }}" wire:model="newAlert"
                                style="pointer-events: auto;"> <!-- Ensure input is always clickable -->
                            <br />
                            @error('newAlert')
                                <font color="red">{{ $message }}</font>
                            @enderror
                            @if($editingAlertId)
                                <button class="btn btn-success" wire:click.prevent="updateAlert">Update</button>
                                <button class="btn btn-secondary" wire:click="cancelEdit">Cancel</button>
                            @else
                                <button class="btn btn-outline-dark" wire:click="addAlert">Add Alert</button>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-1">
                      @foreach(['po' => 'Purchase Order', 'con' => 'Confirmation', 'pac' => 'Packing', 'inv' => 'Invoice', 'cre' => 'Credit'] as $value => $label)
                            <div class="form-check form-check-inline mb-0" style="margin-right: 0;">
                                <input type="checkbox" class="form-check-input" id="type-{{ $value }}" value="{{ $value }}"
                                    wire:model="alertTypes"
                                    wire:key="alert-type-{{ $value }}-{{ $editingAlertId ?? 'new' }}">
                                <label class="form-check-label" for="type-{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                        @error('alertTypes')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade @if($showProfilePopup) show d-block @endif" id="profileModal" tabindex="-1"
        style="@if($showProfilePopup) display: block; @endif" role="dialog">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 400px; width: 50%;">
            <div class="modal-content" style="background: #f0f8ff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
                    style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                    <label class="modal-title fw-bold text-dark m-0" style="font-size: 16px;">
                        <i class="fa fa-user-circle"></i> Customer Profile Requirements</label>
                    <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
    onclick="event.stopPropagation(); @this.call('closeProfilePopup');">Close</button>
                </div>

                <div class="modal-body pt-2 px-4" style="max-height: 70vh; overflow-y: auto;">
                    @if(!empty($profileMessages))
                        @foreach($profileMessages as $profile)
                            <div class="mb-3">
                                @foreach($profile->details as $detail)
                                    @if(str_contains($detail->viewable, 'cre'))
                                        <div class="pb-2 mb-2 border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <div style="width: 95%;">
                                                    <strong>{{ $loop->iteration }}.</strong>
                                                    <span style="font-size: 13px; word-wrap: break-word;">{{ $detail->reqs }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted mb-3" style="font-size: 13px;">No profile requirements found.</div>
                    @endif
                </div>
            </div>
        </div>
        </div>

<style>
/* Modal dragging styles */
.modal {
    overflow: visible !important;
}

.modal-dialog.draggable-modal {
    position: fixed;
    margin: 0;
    pointer-events: auto;
    cursor: default;
}

.modal-content {
    box-shadow: 0 5px 15px rgba(0,0,0,.5);
}

.modal-drag-handle {
    cursor: move !important;
    cursor: grab !important;
    user-select: none !important;
}

.modal-drag-handle:active {
    cursor: grabbing !important;
}

/* Ensure modals are positioned correctly */
#alertModal .modal-dialog,
#profileModal .modal-dialog {
    margin: 0;
    position: fixed;
    top: 50px;
    left: 50px;
}

/* Prevent text selection while dragging */
body.dragging-modal {
    user-select: none !important;
    -webkit-user-select: none !important;
}
</style>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
// Sirf Lookup ID Field - Arrow Keys + Click Outside to Close
document.addEventListener('DOMContentLoaded', function() {
    let selectedIndex = -1;
    
    function getLookupDropdown() {
        const input = document.querySelector('input[wire\\:model="search"]');
        if (input && input.parentElement) {
            return input.parentElement.querySelector('ul.list-group');
        }
        return null;
    }
    
    function hideDropdown() {
        const dropdown = getLookupDropdown();
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }
    
    function showDropdown() {
        const dropdown = getLookupDropdown();
        if (dropdown && dropdown.children.length > 0) {
            dropdown.style.display = '';
        }
    }
    
    function updateSelectedItem() {
        const dropdown = getLookupDropdown();
        if (!dropdown) return;
        
        const items = dropdown.querySelectorAll('li');
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('active');
                item.style.backgroundColor = '#0d6efd';
                item.style.color = 'white';
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('active');
                item.style.backgroundColor = '';
                item.style.color = '';
            }
        });
    }
    
    // Keyboard events for Lookup Input
    document.addEventListener('keydown', function(e) {
        const input = document.querySelector('input[wire\\:model="search"]');
        if (!input || document.activeElement !== input) return;
        
        const dropdown = getLookupDropdown();
        if (!dropdown) return;
        
        const items = dropdown.querySelectorAll('li');
        if (items.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelectedItem();
            showDropdown();
        }
        else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            updateSelectedItem();
            showDropdown();
        }
        else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                items[selectedIndex].click();
                hideDropdown();
                selectedIndex = -1;
            }
        }
        else if (e.key === 'Escape') {
            hideDropdown();
            selectedIndex = -1;
        }
    });
    
    // Click anywhere outside to close dropdown
    document.addEventListener('click', function(e) {
        const input = document.querySelector('input[wire\\:model="search"]');
        
        // Agar click lookup input ke ANDAR nahi hai to hide karo
        if (input && !input.contains(e.target)) {
            hideDropdown();
            selectedIndex = -1;
        }
    });
    
    // Jab lookup input par focus aaye to dropdown dikhao
    const lookupInput = document.querySelector('input[wire\\:model="search"]');
    if (lookupInput) {
        lookupInput.addEventListener('focus', function() {
            setTimeout(() => {
                const dropdown = getLookupDropdown();
                if (dropdown && dropdown.children.length > 0) {
                    showDropdown();
                }
            }, 100);
        });
        
        // Jab user type kare to selection reset karo
        lookupInput.addEventListener('input', function() {
            selectedIndex = -1;
        });
    }
    
    console.log('Lookup ID: Arrow keys + Click outside to close - Activated');
});
</script>

<style>
/* Active item highlight */
ul.list-group li.active {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
}

ul.list-group li:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let activeModal = null;
    let isDragging = false;
    let startX = 0, startY = 0;
    let originalLeft = 0, originalTop = 0;
    
    function makeModalDraggable(modalDialog) {
        if (!modalDialog || modalDialog.hasAttribute('data-draggable-initialized')) {
            return;
        }
        
        const dragHandle = modalDialog.querySelector('.modal-drag-handle');
        if (!dragHandle) {
            return;
        }
        
        // Mark as initialized
        modalDialog.setAttribute('data-draggable-initialized', 'true');
        
        // Make sure modal has position fixed
        modalDialog.style.position = 'fixed';
        
        // Get stored position or set default
        const modalId = modalDialog.closest('.modal')?.id || 'default';
        const storedLeft = localStorage.getItem(`modal-${modalId}-left`);
        const storedTop = localStorage.getItem(`modal-${modalId}-top`);
        
        if (storedLeft && storedTop) {
            modalDialog.style.left = storedLeft + 'px';
            modalDialog.style.top = storedTop + 'px';
        } else {
            // Set default position based on modal type
            if (modalId === 'alertModal') {
                modalDialog.style.left = '100px';
                modalDialog.style.top = '80px';
            } else if (modalId === 'profileModal') {
                modalDialog.style.left = '150px';
                modalDialog.style.top = '150px';
            } else {
                modalDialog.style.left = '50px';
                modalDialog.style.top = '50px';
            }
        }
        
        // Mouse down on handle
        dragHandle.addEventListener('mousedown', function(e) {
            // Don't drag if clicking on buttons, inputs, or interactive elements
            if (e.target.tagName === 'BUTTON' || 
                e.target.tagName === 'INPUT' || 
                e.target.tagName === 'SELECT' ||
                e.target.tagName === 'TEXTAREA' ||
                e.target.closest('button') ||
                e.target.closest('input') ||
                e.target.closest('a')) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            activeModal = modalDialog;
            isDragging = true;
            
            // Get current position
            const rect = modalDialog.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            
            // Store original position
            originalLeft = rect.left;
            originalTop = rect.top;
            
            // Set position if not set
            if (!modalDialog.style.left) {
                modalDialog.style.left = originalLeft + 'px';
            }
            if (!modalDialog.style.top) {
                modalDialog.style.top = originalTop + 'px';
            }
            
            // Add dragging class to body
            document.body.classList.add('dragging-modal');
            
            // Bring modal to front
            const modal = modalDialog.closest('.modal');
            if (modal) {
                modal.style.zIndex = '9999';
            }
        });
        
        // Prevent drag handle from interfering with child elements
        dragHandle.querySelectorAll('button, input, select, textarea, a').forEach(el => {
            el.addEventListener('mousedown', function(e) {
                e.stopPropagation();
            });
        });
    }
    
    // Mouse move
    document.addEventListener('mousemove', function(e) {
        if (!isDragging || !activeModal) return;
        
        e.preventDefault();
        
        let newLeft = e.clientX - startX;
        let newTop = e.clientY - startY;
        
        // Constrain to viewport
        const modalRect = activeModal.getBoundingClientRect();
        const maxLeft = window.innerWidth - modalRect.width;
        const maxTop = window.innerHeight - modalRect.height;
        
        newLeft = Math.max(0, Math.min(newLeft, maxLeft));
        newTop = Math.max(0, Math.min(newTop, maxTop));
        
        activeModal.style.left = newLeft + 'px';
        activeModal.style.top = newTop + 'px';
    });
    
    // Mouse up
    document.addEventListener('mouseup', function(e) {
        if (isDragging && activeModal) {
            isDragging = false;
            document.body.classList.remove('dragging-modal');
            
            // Save position
            const modalId = activeModal.closest('.modal')?.id;
            if (modalId) {
                const left = parseInt(activeModal.style.left);
                const top = parseInt(activeModal.style.top);
                if (!isNaN(left) && !isNaN(top)) {
                    localStorage.setItem(`modal-${modalId}-left`, left);
                    localStorage.setItem(`modal-${modalId}-top`, top);
                }
            }
            
            activeModal = null;
        }
    });
    
    // Function to initialize all visible modals
    function initializeAllModals() {
        document.querySelectorAll('.modal.show, .modal.d-block').forEach(modal => {
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                // Add draggable class if not present
                if (!modalDialog.classList.contains('draggable-modal')) {
                    modalDialog.classList.add('draggable-modal');
                }
                makeModalDraggable(modalDialog);
            }
        });
    }
    
    // Watch for modal visibility changes
    const modalObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const modal = mutation.target;
                if (modal.classList.contains('show') || modal.classList.contains('d-block')) {
                    setTimeout(() => {
                        const modalDialog = modal.querySelector('.modal-dialog');
                        if (modalDialog) {
                            if (!modalDialog.classList.contains('draggable-modal')) {
                                modalDialog.classList.add('draggable-modal');
                            }
                            makeModalDraggable(modalDialog);
                        }
                    }, 100);
                }
            }
        });
    });
    
    // Observe all modals
    document.querySelectorAll('.modal').forEach(modal => {
        modalObserver.observe(modal, { attributes: true });
    });
    
    // Listen for Livewire events
    if (typeof Livewire !== 'undefined') {
        Livewire.on('showAlertPopup', () => {
            setTimeout(initializeAllModals, 200);
        });
        
        Livewire.on('showProfilePopup', () => {
            setTimeout(initializeAllModals, 200);
        });
        
        // Also listen for Livewire updates
        document.addEventListener('livewire:update', function() {
            setTimeout(initializeAllModals, 150);
        });
        
        document.addEventListener('livewire:load', function() {
            setTimeout(initializeAllModals, 150);
        });
    }
    
    // Initial setup
    setTimeout(initializeAllModals, 200);
    
    // Also setup when clicking on modal (for dynamic modals)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.modal')) {
            setTimeout(initializeAllModals, 50);
        }
    });
    
    console.log('Drag functionality initialized');
});
</script>
        <style>
    .form-check-inline .form-check-input {
        margin-right: 0;
    }
    .form-check-inline .form-check-label {
        margin-left: 0;
        padding-left: 0;
    }
</style>
</div>