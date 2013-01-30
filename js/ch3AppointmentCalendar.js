function appointmentCalendarClass(grid,timeGrid) {

	this.grid = null;
	this.timeGrid = null;
	this.appointmentId = null;
	this.appAccordion = null;

	var self = this;

	if (grid) this.grid = grid;
	if (timeGrid) this.timeGrid = timeGrid;

	this.onDblClicked = function(rowId,colIndex,callbackId) {
		var providerId = this.grid.getUserData(this.grid.getRowId(0),"providerId");
		if (providerId == "placeHolderId") {
			alert("No filter defined.");
			return;
		}
		if (this.appointmentId && this.appointmentId.length > 0) {
			var win = this.openAppointmentWindow({
				"appointmentId":this.appointmentId,
				"callbackId":callbackId,
			},"edit");
			win.setText("Edit Appointment");
			this.appointmentId = null;
		}
		else {
			this.addAppointment(rowId,colIndex,callbackId);
		}
	};

	this.openAppointmentWindow = function(params,action) {
		if (!action) action = "new";
		var query = this.buildQuery(params);
		var url = globalBaseUrl+"/calendar.raw/"+action+"-appointment?"+query;
		var winAddAppointment = globalCreateWindow("windowAppointmentId",[],url,"Add New Appointment",800,500,{attachURL:false,setModal:false});
		this.appAccordion = winAddAppointment.attachAccordion();
		this.appAccordion.skin = "dhx_blue";
		this.appAccordion.setIconsPath(globalBaseUrl+"/img/");
		this.appAccordion.addItem("appointmentInfo","Appointment Information");
		this.appAccordion.addItem("pointOfSale","Account Status & Eligibility");
		this.appAccordion.attachEvent("onBeforeActive",function(itemId){
			if (itemId == "pointOfSale" && window.appointmentHasChanges && window.appointmentHasChanges()) {
				if (!confirm("Your changes will be saved automatically, Continue?")) return false;
				if (window.calendarMakeAppointment) {
					calendarMakeAppointment(false,function(data){
						if (data.appointmentId) {
							params = {"appointmentId":data.appointmentId};
							winAddAppointment.setText("Edit Appointment");
							self.appAccordion.cells(itemId).open();
							self.appAccordion.callEvent("onActive",[itemId]);
						}
					});
				}
				return false;
			}
			return true;
		});
		this.appAccordion.attachEvent("onActive",function(itemId){
			var query = self.buildQuery(params);
			switch (itemId) {
				case "appointmentInfo":
					self.openAppointmentAccordion(itemId+"ContainerId",itemId,url);
					break;
				case "pointOfSale":
					self.openAppointmentAccordion(itemId+"ContainerId",itemId,globalBaseUrl+"/calendar.raw/point-of-sale?"+query);
					break;
			}
		});

		this.appAccordion.cells("appointmentInfo").open();
		this.openAppointmentAccordion("appointmentInfoContainerId","appointmentInfo",url);
		return winAddAppointment;
	};

	this.openAppointmentAccordion = function(divId,cellId,url) {
		var newDiv = document.createElement("div");
		newDiv.setAttribute("id",divId);
		dojo.setInnerHTML(newDiv,"<img src=\""+globalBaseUrl+"/img/loading.gif\" alt=\"Loading...\" style=\"margin:10px;\" />");
		this.appAccordion.cells(cellId).attachObject(newDiv);
		dojo.xhrGet({
			url: url,
			handleAs: "text",
			load: function(data,ioArgs) {
				dojo.setInnerHTML(newDiv,data);
				self.appAccordion.cells(cellId).attachObject(newDiv);
				return data;
			},
			error: function(response, ioArgs) {
				console.error("HTTP status code: ", ioArgs.xhr.status);
				return response;
			}
		});
	};

	this.addAppointment = function(rowId,colIndex,callbackId) {
		var rowIndex = this.grid.getRowIndex(rowId);
		var timeStart = this.timeGrid.cellByIndex(rowIndex,colIndex).getValue();
		var id = this.grid.getRowId(0);
		var params = {
			"callbackId": callbackId,
			"appointment[date]": this.grid.getUserData(id,"date"),
			"appointment[start]": timeStart,
			"appointment[providerId]": this.grid.getUserData(id,"providerId"),
			"appointment[roomId]": this.grid.getUserData(id,"roomId"),
		};
		this.openAppointmentWindow(params);
	};

	this.buildQuery = function(params) {
		var query = [];
		if (params) {
			for (var i in params) {
				query.push(i+"="+params[i]);
			}
		}
		return query.join("&");
	};

	this.onDragged = function(idFrom,idTo,gridFrom,gridTo,colIndexFrom,colIndexTo,callbackId) {
		var appointmentId = this.appointmentId
		this.appointmentId = null;
		if (!appointmentId) {
			alert("No appointment selected.");
			return false;
		}

		var rowIndexTo = gridTo.getRowIndex(idTo);
		var timeTo = calendarTimeGrid.cellByIndex(rowIndexTo,colIndexTo).getValue();
		this._processChangeTimeAppointment(appointmentId,timeTo,false,callbackId);
		return false;
	};

	this._processChangeTimeAppointment = function(appointmentId,timeTo,forced,callbackId) {
		forced = (forced)?"1":"0";
		dojo.xhrPost({
			url: globalBaseUrl + "/calendar.raw/change-time-appointment",
			handleAs: "json",
			content: {
				"appointmentId": appointmentId,
				"time": timeTo,
				"forced": forced,
			},
			load: function (data) {
				if (data.error) {
					alert(data.error);
					return;
				}
				if (data.confirmation) {
					if (confirm(data.confirmation  + "\n\nContinue?")) self._processChangeTimeAppointment(appointmentId,timeTo,true,callbackId);
					return;
				}
				if (callbackId && globalCallbackList[callbackId]) {
					var callback = globalCallbackList[callbackId];
					callback.object["responseData"] = data;
					callback.func.apply(callback.object,callback.params);
					globalCallbackList[callbackId] = null;
				}
			},
			error: function (error) {
				alert(error);
				console.error("Error: ", error);
			}
		});
	};

}
