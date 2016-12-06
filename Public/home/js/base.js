define("responseFrame", ["jquery"], function(t) {
	function e() {
		this.win = t(window), this.rootEle = t("html"), this.bodyEle = t("body"), this.nxContainer = t("#nxContainer"), this.nxHeader = t("#nxHeader"), this.hdFixed = this.nxHeader.find(".hd-fixed-wraper"), this.hdMain = this.nxHeader.find(".hd-main"), this.nxSlidebar = t("#nxSlidebar"), this.subNav = t("#frameFixedNav"), this.fixedLayer = t('<div id="fixedLayer">').prependTo(this.bodyEle), this.fixedWidth = 0, this.sidebarWidth = 0, this.webpagerWidth = 0, this.headerCombined = !1, this.webpagerAutoFolded = !1, nx.store.get("webpager.user") !== nx.user.id.toString() && (nx.store.set("webpager.user", nx.user.id), nx.store.remove("webpager.html")), this.resizeView(), this.loadWebpager(), t(window).on("resize/50", this.resizeView.bind(this)).on("scroll/1", this.combineNav.bind(this)), t(document).on("webpager:fold", function() {
			this.resizeViewForWebpager(!0)
		}.bind(this)).on("webpager:unfold", function() {
			this.resizeViewForWebpager(!1)
		}.bind(this)).on("ready newsfeed:single newsfeed:double", this.resizeView.bind(this)), t(window).on("unload", function() {
			window.scrollTo(0, 0)
		})
	}
	return e.prototype = {
		_switchClass: function(t) {
			this.nxContainer.removeClass("nx-normalViewport nx-narrowViewport nx-freeViewport").addClass(t)
		},
		_changeClass: function() {
			var t = this.win.width(),
				e = this.rootEle.prop("class").match(/nx-main(\d+)/);
			e ? (e = nx.data.isDoubleFeed && t >= 1260 ? 880 : Number(e[1]), e += 60, 80 + e + 240 > t ? this._switchClass("nx-freeViewport") : 160 + e + 240 > t ? this._switchClass("nx-narrowViewport") : this._switchClass("nx-normalViewport")) : this._switchClass("nx-freeViewport")
		},
		_computeSize: function() {
			var t = this._isWPOpen(),
				e = this.nxContainer.hasClass("nx-freeViewport");
			e || this.nxContainer.addClass("nx-webpager-" + (t ? "unfold" : "fold")), this.webpagerWidth = !t || e ? 0 : 240, this.sidebarWidth = Number(this.nxSlidebar.outerWidth()), this.fixedWidth = this.win.width() - this.webpagerWidth, this.nxSlidebar.height(this.win.height() - this.nxHeader.height()), this.fixedLayer.width(this.fixedWidth - (t && e ? 240 : 0)), this.hdFixed.width(this.fixedWidth), this.subNav.width(this.fixedWidth - this.sidebarWidth)
		},
		_isWPOpen: function() {
			return nx.webpager.disable ? !1 : "undefined" == typeof nx.webpager.fold ? "1" !== nx.cookie("wp_fold") : !nx.webpager.fold
		},
		resizeViewForWebpager: function(e) {
			if (this.nxContainer.hasClass("nx-freeViewport")) return this.nxContainer.removeClass("nx-webpager-fold nx-webpager-unfold"), void this.fixedLayer.width(this.fixedWidth - (e ? 0 : 240));
			if (!this.webpagerAutoFolded) {
				var i = this;
				this.nxContainer.hasClass("nx-narrowViewport") && (this.sidebarWidth = this.sidebarWidth ? this.sidebarWidth + (e ? 80 : -80) : 0), this.webpagerWidth = e ? 0 : 240, this.fixedWidth = this.win.width() - this.webpagerWidth, this.fixedLayer.velocity({
					width: this.fixedWidth
				}), this.nxContainer.velocity({
					"margin-right": this.webpagerWidth
				}, function() {
					i.nxContainer.removeClass("nx-webpager-fold nx-webpager-unfold").addClass("nx-webpager-" + (e ? "fold" : "unfold")), t.event.trigger("mainframe:reflow")
				}), this.hdFixed.velocity({
					width: this.fixedWidth
				}, function() {
					var e = t("#nxHeader .pop-vip");
					e.length && (e.insertAfter("#nxHeader .hd-account-action-vip").show(), e.data("bind") || (e.find(".pop-vip-close").one("click", function() {
						e.remove()
					}), e.data("bind", !0)))
				}), this.subNav.velocity({
					width: this.fixedWidth - this.sidebarWidth
				})
			}
		},
		resizeView: function(e) {
			this._changeClass(), this._computeSize(), t.event.trigger("mainframe:reflow")
		},
		loadWebpager: function() {
			if (240 === this.webpagerWidth && 0 === t("#webpager-holder").length && 0 === t("body > #webpager").length) {
				var e = t("<div>").attr({
					id: "webpager-holder"
				}).html('<div id="webpager"><div class="nav-holder"></div><div class="subnav-holder"></div></div>').height(this.win.height()).insertBefore(this.nxContainer),
					i = nx.store.get("webpager.html"),
					n = nx.store.get("webpager.css");
				i && n && nx.load(n, function() {
					var n = t(i);
					e.before(n), t("#webpager").height() < t(window).height() ? n.remove() : e.empty().append(n)
				})
			}
		},
		combineNav: function() {
			var e = t(document).scrollTop(),
				i = nx.browser.msie && 7 === nx.browser.versionNumber,
				n = this;
			e > 150 ? this.headerCombined || (this.headerCombined = !0, t("#search-result-box").is(":visible") && t("#hd-search").blur(), this.subNav.velocity("stop").velocity({
				top: "0px"
			}, function() {
				t(this).addClass("fix-app-nav"), i && (n.nxHeader.css("z-index", "-1"), n.hdMain.find(".hd-nav").hide())
			}), i || this.hdMain.find(".hd-nav").velocity("stop").velocity("fadeOut")) : this.headerCombined && (this.headerCombined = !1, this.subNav.removeClass("fix-app-nav").velocity("stop").velocity({
				top: "50px"
			}), i ? (this.hdMain.find(".hd-nav").show(), this.nxHeader.css("z-index", "auto")) : this.hdMain.find(".hd-nav").velocity("stop").velocity("fadeIn"))
		}
	}, {
		initFrameSize: function() {
			this.fm = new e
		},
		resizeView: function(t) {
			return this.fm.resizeView(t)
		},
		combineNav: function() {
			return this.fm.combineNav()
		}
	}
}), define("mainframe", function(require) {
	var i = require("sideBarControls"),
		a = require("sidebar");
	i.init(), a.init(), t(".v7-feedback").css("display", "block").click(function(t) {
		t.stopPropagation(), o.init()
	}), t("#lawInfo").click(function() {
		c.init()
	})
}), require(["jquery", "responseFrame"], function(t, e) {
	var i = setInterval(function() {
		t("#nxHeader").length && (clearInterval(i), "object" != typeof e.fm && e.initFrameSize())
	}, 1);
	t(function() {
		require(["mainframe"], t.noop)
	}), window._developer_no_webpager = !0
}), define("sideBarControls", ["jquery", "ui/scrollbar"], function(t) {
	var e, i = {
			appBarScroll: function() {
				e = t.ui.scrollbar({
					barClass: "scroll-cont",
					scrollEle: ".app-nav-wrap",
					stopBubble: !1,
					wheelSpeed: 100
				}, t("#nxSlidebar")[0]), t(window).bind("resize/100", function() {
					e.update()
				})
			},
			showMyManageApp: function() {
				t("#my-manage-app").click(function(i) {
					i.preventDefault();
					var n = t(this).siblings(".app-nav-list");
					if (0 !== n.find("li").length && !n.is(":animated"))
						if (n.is(":visible")) {
							var a = t(this);
							n.slideUp("fast", function() {
								a.removeClass("app-manage-open"), t(".nx-sidebar").trigger("resize"), t("#my-manage-app").attr("data-tip", "我管理的"), t("#my-manage-app .app-title").text("我管理的"), e.update()
							})
						} else {
							var a = t(this);
							n.slideDown("fast", function() {
								a.addClass("app-manage-open"), t(".nx-sidebar").trigger("resize"), t("#my-manage-app").attr("data-tip", "收起"), t("#my-manage-app .app-title").text("收起"), e.update()
							})
						}
				})
			},
			appNavTips: function() {
				require(["ui/tooltip"], function() {
					t(".app-nav-list").tooltip({
						tooltipClass: "app-nav-tooltip nx-tooltip",
						position: {
							my: "left-18 center",
							at: "right center"
						},
						items: ".app-nav-item",
						content: function() {
							var e = t(this).data("tip") || "";
							return '<div class="ui-tooltip-arr"></div>' + t("<a>").text(e).html()
						},
						beforeOpen: function() {
							return t(".nx-sidebar .app-title").is(":visible") ? !0 : !1
						},
						hide: !1,
						openDelay: 0
					})
				})
			},
			init: function() {
				this.appBarScroll(), this.showMyManageApp(), this.appNavTips()
			}
		};
	return i
}), define("sidebar", ["jquery"], function(t) {
	var e = function() {
		function e() {
			return
		}
		var i = this,
			n = t(window),
			a = t(".nx-right");
		a.length && (i.msie = "msie" === nx.browser.name && nx.browser.versionNumber < 9 ? !0 : !1, i.fixedEvent || (i.fixedEvent = !0, t(document).on("webpager:fold", function() {
			a.data("windowscroll") && !t("#nxContainer").hasClass("nx-freeViewport") && a.velocity({
				right: "-=120"
			})
		}), t(document).on("webpager:unfold", function() {
			a.data("windowscroll") && !t("#nxContainer").hasClass("nx-freeViewport") && a.velocity({
				right: "+=120"
			})
		}), t(document).on("mainframe:reflow", function() {
			a.data("windowscroll") && (nx.data.isDoubleFeed && t(window).width() >= 1260 ? (a.data("windowscroll", null), a.css({
				position: "relative",
				top: 0,
				bottom: 0,
				right: 0
			}), a.prev(".nx-right-placeholder").remove()) : a.css("right", (t(".bd-main").width() - t(".bd-content").width()) / 2 + parseInt(t("#nxContainer").css("margin-right"), 10)))
		}), n.on("feedchange", function(t, e) {
			"empty" === e && a.data("windowscroll") ? (a.data("windowscroll", null), a.css({
				position: "relative",
				top: 0,
				bottom: 0,
				right: 0
			}), a.prev(".nx-right-placeholder").remove()) : n.trigger("scroll")
		}), n.on("scroll/1", function() {
			if (!a.parents(".double-cols-feed").length) {
				var e = n.scrollTop();
				if (a.data("windowscroll"))
					if (n.scrollTop() < a.data("windowscroll")) a.data("windowscroll", null), a.css({
						position: "relative",
						top: 0,
						bottom: 0,
						right: 0
					}), a.prev(".nx-right-placeholder").remove();
					else {
						var i = t(".ft-wrapper").outerHeight(!0);
						n.viewport().check(i).bottom ? "0px" === a.css("bottom") && a.css({
							top: "auto",
							bottom: i
						}) : a.css("bottom") === i + "px" && (a.height() + 50 > n.height() ? a.css({
							top: "auto",
							bottom: 0
						}) : a.css({
							top: 50,
							bottom: "auto"
						}))
					} else a.height() < t(".bd-content").height() && e > 115 && a.get(0).getBoundingClientRect().bottom - n.height() < 1 && (a.height() + 50 > n.height() ? (a.data("windowscroll", a.offset().top + a.height() - n.height()), a.css({
						top: "auto",
						bottom: 0
					})) : (a.data("windowscroll", 115), a.css({
						top: 50,
						bottom: "auto"
					})), a.css({
						position: "fixed",
						right: n.width() - a.get(0).getBoundingClientRect().right,
						"z-index": 99
					}), a.prev(".nx-right-placeholder").remove().end().before('<div class="nx-right-placeholder">'))
			}
		}), n.trigger("scroll"), t(nx.data.feed).on("sortByTime", e), t(nx.data.feed).on("sortBySmart", e), t(nx.data.feed).on("contentFocus", e), t(nx.data.feed).on("groupFocus", e), t(nx.data.feed).on("friendsNewsFeed", e), t(nx.data.feed).on("friendsOriginal", e), t(nx.data.feed).on("newsfeedFilter", e), i.msie && (t(nx.data.feed).on("feedLoading", function() {
			t(".ft-wrapper").css("position", "")
		}), t(nx.data.feed).on("feedRendered", function() {
			t(".ft-wrapper").css("position", "relative")
		}))))
	};
	return {
		init: function() {
			new e
		}
	}
})