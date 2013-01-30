/*****************************************************************************
*       Chart2D.js
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/
dojo.provide("custom.Chart2D");
dojo.declare("custom.Chart2D", dojox.charting.Chart2D, {
	createGraph: function(plotArgs) {
		this.addAxis("x",{labels:plotArgs.xLabels});
		this.addAxis("y",{vertical:true,labels:plotArgs.yLabels});
		for (var index in plotArgs.plots) {
			var plot = plotArgs.plots[index];
			var markers = true;
			if (typeof(plot.markers) != 'undefined') {
				markers = plot.markers;
			}
			this.addPlot(plot.name,{type:"Lines",markers:markers});
			this.addSeries(plot.seriesName,plot.series,{plot:plot.name,stroke:{color:plot.color}});
		}
		this.render();
	},
});
