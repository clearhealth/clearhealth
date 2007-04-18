The following variations have been tested:
	* x12_al_bcbs
	* x12_al_ens_unitedhealthcare
	* x12_al_healthfusion_aetna
	* x12_al_medicaid
	* x12_altricare
	* x12_ca_medi-cal
	* x12_ca_nhc
	* x12_ca_snhic_medicare

In addition, x12_generic can be used for generic claims that are to be submitted to clearing houses,
or it can be used as the base to create new variations.


== Creating new variations ==
To create an new variation, simply copy one of the working variation directories into another 
directory that starts with "x12_" or "hcfa_", depending on whether the claim is electronic or paper.
Once the directory has been copied, rename the two variation files - "x12_<variation>.html" and
"x12_<variation>_header.html" to match the new directory name.

Example:
	New directory is named: x12_az_bcbs
	Files should be named: x12_az_bcbs.html and x12_az_bcbs_header.html

From there, you are ready to generate a variation and start editing as necessary.
