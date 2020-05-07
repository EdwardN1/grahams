var $jscomp = $jscomp || {};
$jscomp.scope = {};
$jscomp.arrayIteratorImpl = function (c) {
    var b = 0;
    return function () {
        return b < c.length ? {done: !1, value: c[b++]} : {done: !0}
    }
};
$jscomp.arrayIterator = function (c) {
    return {next: $jscomp.arrayIteratorImpl(c)}
};
$jscomp.ASSUME_ES5 = !1;
$jscomp.ASSUME_NO_NATIVE_MAP = !1;
$jscomp.ASSUME_NO_NATIVE_SET = !1;
$jscomp.SIMPLE_FROUND_POLYFILL = !1;
$jscomp.defineProperty = $jscomp.ASSUME_ES5 || "function" == typeof Object.defineProperties ? Object.defineProperty : function (c, b, e) {
    c != Array.prototype && c != Object.prototype && (c[b] = e.value)
};
$jscomp.getGlobal = function (c) {
    c = ["object" == typeof window && window, "object" == typeof self && self, "object" == typeof global && global, c];
    for (var b = 0; b < c.length; ++b) {
        var e = c[b];
        if (e && e.Math == Math) return e
    }
    return globalThis
};
$jscomp.global = $jscomp.getGlobal(this);
$jscomp.SYMBOL_PREFIX = "jscomp_symbol_";
$jscomp.initSymbol = function () {
    $jscomp.initSymbol = function () {
    };
    $jscomp.global.Symbol || ($jscomp.global.Symbol = $jscomp.Symbol)
};
$jscomp.SymbolClass = function (c, b) {
    this.$jscomp$symbol$id_ = c;
    $jscomp.defineProperty(this, "description", {configurable: !0, writable: !0, value: b})
};
$jscomp.SymbolClass.prototype.toString = function () {
    return this.$jscomp$symbol$id_
};
$jscomp.Symbol = function () {
    function c(e) {
        if (this instanceof c) throw new TypeError("Symbol is not a constructor");
        return new $jscomp.SymbolClass($jscomp.SYMBOL_PREFIX + (e || "") + "_" + b++, e)
    }

    var b = 0;
    return c
}();
$jscomp.initSymbolIterator = function () {
    $jscomp.initSymbol();
    var c = $jscomp.global.Symbol.iterator;
    c || (c = $jscomp.global.Symbol.iterator = $jscomp.global.Symbol("Symbol.iterator"));
    "function" != typeof Array.prototype[c] && $jscomp.defineProperty(Array.prototype, c, {
        configurable: !0, writable: !0, value: function () {
            return $jscomp.iteratorPrototype($jscomp.arrayIteratorImpl(this))
        }
    });
    $jscomp.initSymbolIterator = function () {
    }
};
$jscomp.initSymbolAsyncIterator = function () {
    $jscomp.initSymbol();
    var c = $jscomp.global.Symbol.asyncIterator;
    c || (c = $jscomp.global.Symbol.asyncIterator = $jscomp.global.Symbol("Symbol.asyncIterator"));
    $jscomp.initSymbolAsyncIterator = function () {
    }
};
$jscomp.iteratorPrototype = function (c) {
    $jscomp.initSymbolIterator();
    c = {next: c};
    c[$jscomp.global.Symbol.iterator] = function () {
        return this
    };
    return c
};
$jscomp.iteratorFromArray = function (c, b) {
    $jscomp.initSymbolIterator();
    c instanceof String && (c += "");
    var e = 0, f = {
        next: function () {
            if (e < c.length) {
                var a = e++;
                return {value: b(a, c[a]), done: !1}
            }
            f.next = function () {
                return {done: !0, value: void 0}
            };
            return f.next()
        }
    };
    f[Symbol.iterator] = function () {
        return f
    };
    return f
};
$jscomp.polyfill = function (c, b, e, f) {
    if (b) {
        e = $jscomp.global;
        c = c.split(".");
        for (f = 0; f < c.length - 1; f++) {
            var a = c[f];
            a in e || (e[a] = {});
            e = e[a]
        }
        c = c[c.length - 1];
        f = e[c];
        b = b(f);
        b != f && null != b && $jscomp.defineProperty(e, c, {configurable: !0, writable: !0, value: b})
    }
};
$jscomp.polyfill("Array.prototype.keys", function (c) {
    return c ? c : function () {
        return $jscomp.iteratorFromArray(this, function (b) {
            return b
        })
    }
}, "es6", "es3");
(function (c) {
    function b() {
        var a = !1, d;
        this.settings = {
            appendHelpButton: !0,
            displayHelpButton: !0,
            isExternalPage: !0,
            devMode: !1,
            targetElement: document.body,
            elementForOnlineDisplay: void 0,
            elementForOfflineDisplay: void 0,
            defaultMinimizedText: "",
            disabledMinimizedText: "",
            defaultAssistiveText: "",
            loadingText: "Loading",
            showIcon: void 0,
            enabledFeatures: [],
            entryFeature: "FieldService",
            storageDomain: document.domain,
            language: void 0,
            linkAction: {feature: void 0, name: void 0, valid: !1},
            linkActionParameters: {},
            useCustomAuthentication: !1,
            allowGuestUsers: !1,
            requireSLDS: !1
        };
        this.auth = {};
        this.validLinkActions = {};
        this.isMasterAndHasSlaves = !1;
        Object.defineProperty(this.auth, "oauthToken", {
            get: function () {
                return d
            }, set: function (a) {
                this.validateHeaderValue(a) ? (d = a) ? (this.setSessionData("ESW_OAUTH_TOKEN", a), this.checkAuthentication()) : this.deleteSessionData("ESW_OAUTH_TOKEN") : this.error('"' + a + '" is not a valid OAuth token.')
            }.bind(this)
        });
        this.featureScripts = {};
        this.storedEventHandlers = {};
        this.messageHandlers = {};
        this.storageKeys = ["ESW_BODY_SCROLL_POSITION",
            "ESW_IS_MINIMIZED", "ESW_MINIMIZED_TEXT", "ESW_OAUTH_TOKEN"];
        this.defaultSettings = {};
        this.snippetSettingsFile = {};
        this.eswFrame = void 0;
        this.availableFeatures = ["script", "session"];
        this.outboundMessagesAwaitingIframeLoad = [];
        this.pendingMessages = {};
        this.iframeScriptsToLoad = [];
        this.isAuthenticationRequired = this.isIframeReady = this.hasSessionDataLoaded = this.componentInitInProgress = this.domInitInProgress = !1;
        this.loginPendingSerializedData = void 0;
        Object.defineProperty(this, "isButtonDisabled", {
            get: function () {
                return a
            },
            set: function (d) {
                a = d;
                this.onButtonStatusChange()
            }.bind(this), configurable: !0
        });
        this.setupMessageListener();
        this.getLinkActionData()
    }

    function e() {
        return window.$A && "function" === typeof window.$A.get && window.$A.get("$Site")
    }

    var f = [".salesforce.com", ".force.com", ".sfdc.net"];
    b.prototype.getLightningOutParamsObj = function () {
        var a;
        embedded_svc.config && embedded_svc.config.additionalSettings && embedded_svc.config.additionalSettings.labelsLanguage ? a = {guestUserLang: embedded_svc.config.additionalSettings.labelsLanguage} :
            embedded_svc.settings.language && "" !== embedded_svc.settings.language.trim() && (a = {guestUserLang: embedded_svc.settings.language});
        return a
    };
    b.prototype.adjustCommunityStorageDomain = function () {
        this.isCommunityDomain(this.settings.storageDomain) && this.settings.storageDomain === document.domain && (this.settings.storageDomain = this.settings.storageDomain + "/" + window.location.pathname.split("/")[1])
    };
    b.prototype.loadLightningOutScripts = function (a) {
        if ("function" !== typeof Promise) this.loadScriptFromDirectory("common",
            "promisepolyfill", function () {
                return this.loadLightningOutScripts(a)
            }.bind(this), !0); else return new Promise(function (d, g) {
            try {
                var b = a && a.baseCoreURL ? a.baseCoreURL : embedded_svc.settings.baseCoreURL;
                if (window.$Lightning) d("Lightning Out is already loaded on this page."); else if (e()) d("Communities context does not require Lightning Out to use Embedded Service."); else if (b) {
                    var c = document.createElement("script");
                    c.type = "text/javascript";
                    c.src = b + "/lightning/lightning.out.js";
                    c.onload = function () {
                        d("Lightning Out scripts loaded.")
                    };
                    document.getElementsByTagName("head")[0].appendChild(c)
                }
            } catch (h) {
                g(h)
            }
        })
    };
    b.prototype.instantiateLightningOutApplication = function (a) {
        if ("function" !== typeof Promise) this.loadScriptFromDirectory("common", "promisepolyfill", function () {
            return this.instantiateLightningOutApplication(a)
        }.bind(this), !0); else return new Promise(function (d, b) {
            try {
                var g = a && a.communityEndpointURL ? a.communityEndpointURL : embedded_svc.settings.communityEndpointURL;
                var c = a && a.oauthToken ? a.oauthToken : embedded_svc.settings.oauthToken;
                var f = a && a.paramsObj ? a.paramsObj : embedded_svc.getLightningOutParamsObj() || void 0;
                e() ? d("Communities context already has an Aura context.") : window.$Lightning && $Lightning.use("embeddedService:sidebarApp", function () {
                    d("Lightning Out application request complete.")
                }, g, c, f)
            } catch (l) {
                b(l)
            }
        })
    };
    b.prototype.createEmbeddedServiceComponent = function (a) {
        if ("function" !== typeof Promise) this.loadScriptFromDirectory("common", "promisepolyfill", function () {
            return this.createEmbeddedServiceComponent(a)
        }.bind(this), !0);
        else return new Promise(function (d, b) {
            try {
                var g = a && a.attributes ? a.attributes : {configurationData: embedded_svc.settings};
                var c = a && a.locator ? a.locator : embedded_svc.settings.targetElement;
                embedded_svc.preparePageForSidebar();
                window.$Lightning && !document.querySelector(".embeddedServiceSidebar") ? $Lightning.ready($Lightning.createComponent.bind(this, "embeddedService:sidebar", g, c, function (a, g, c) {
                    "SUCCESS" === g ? embedded_svc.utils.addEventHandler("afterInitialization", function () {
                            d("Embedded Service component created.")
                        }) :
                        b(c)
                })) : e() ? window.dispatchEvent(new CustomEvent("embeddedServiceCreateSidebar", {
                    detail: {
                        componentAttributes: g,
                        resolve: d,
                        reject: b
                    }
                })) : "undefined" === typeof window.$Lightning ? d("Lightning Out should be loaded on this page before creating the Embedded Service component.") : d("Embedded Service component already exists.")
            } catch (h) {
                b(h)
            }
        })
    };
    b.prototype.bootstrapEmbeddedService = function (a) {
        if ("function" !== typeof Promise) this.loadScriptFromDirectory("common", "promisepolyfill", function () {
                return this.bootstrapEmbeddedService(a)
            },
            !0); else return new Promise(function (d, b) {
            try {
                embedded_svc.loadLightningOutScripts(a).then(function () {
                    embedded_svc.instantiateLightningOutApplication(a).then(function () {
                        embedded_svc.createEmbeddedServiceComponent(a).then(function () {
                            window.requestAnimationFrame(function () {
                                d("Embedded Service application and component bootstrapped.")
                            })
                        })
                    })
                })
            } catch (k) {
                b(k)
            }
        })
    };
    b.prototype.isInternetExplorer = function () {
        return "ActiveXObject" in window
    };
    b.prototype.outputToConsole = function (a, d, b) {
        if ((b || this.settings.devMode) &&
            console && console[a]) console[a]("[Snap-ins] " + (Array.isArray(d) ? d.join(", ") : d))
    };
    b.prototype.log = function () {
        this.outputToConsole("log", [].slice.apply(arguments))
    };
    b.prototype.error = function (a, d) {
        a ? this.outputToConsole("error", a, d) : this.outputToConsole("error", "esw responed with an unspecified error.", d);
        embedded_svc.utils.fireEvent("error")
    };
    b.prototype.warning = function (a, d) {
        a ? this.outputToConsole("warn", "Warning: " + a, d) : this.outputToConsole("warn", "esw sent an anonymous warning.", d)
    };
    b.prototype.deprecated =
        function (a) {
            this.warning(a + " is deprecated in version " + Number("5.0").toFixed(1) + " and will be removed in version " + (Number("5.0") + 1).toFixed(1))
        };
    b.prototype.getCookie = function (a) {
        var d = document.cookie;
        if (d) {
            var b = d.indexOf(a + "=");
            if (-1 !== b) return b += (a + "=").length, a = d.indexOf(";", b), -1 === a && (a = d.length), d.substring(b, a)
        }
    };
    b.prototype.setCookie = function (a, d, b) {
        a = a + "=" + d + ";";
        b && (b = new Date, b.setFullYear(b.getFullYear() + 10), a += "expires=" + b.toUTCString() + ";");
        document.cookie = a + "path=/;"
    };
    b.prototype.mergeSettings =
        function (a) {
            Object.keys(a).forEach(function (d) {
                void 0 === this.settings[d] && (this.settings[d] = a[d])
            }.bind(this))
        };
    b.prototype.loadFeatureScript = function (a, d) {
        var b = decodeURI(a).toLowerCase();
        -1 === a.indexOf("..") ? this.loadScriptFromDirectory("client", b + ".esw", function () {
            this.featureScripts[a](this);
            this.availableFeatures.push(b);
            embedded_svc.utils.fireEvent("featureLoaded", void 0, a);
            d && d();
            this.processPendingMessages(b)
        }.bind(this)) : this.error('"' + a + '" is not a valid feature name.')
    };
    b.prototype.fireEvent =
        function (a, d) {
            var b = [].slice.apply(arguments).slice(2);
            if (window.embedded_svc && embedded_svc.utils) return embedded_svc.utils.fireEvent(a, d, b);
            this.error("fireEvent should not be called before calling init!");
            return !0
        };
    b.prototype.isValidEntityId = function (a) {
        return "string" === typeof a && (18 === a.length || 15 === a.length)
    };
    b.prototype.getKeyPrefix = function (a) {
        if (this.isValidEntityId(a)) return a.substr(0, 3)
    };
    b.prototype.isOrganizationId = function (a) {
        return "00D" === this.getKeyPrefix(a)
    };
    b.prototype.getESWFrame =
        function () {
            var a = document.getElementById("esw_storage_iframe");
            !this.eswFrame && a && (this.eswFrame = a.contentWindow);
            return this.eswFrame
        };
    b.prototype.isFrameStorageEnabled = function () {
        this.deprecated("isFrameStorageEnabled");
        return !0
    };
    b.prototype.processPendingMessages = function (a) {
        this.pendingMessages[a] && (this.pendingMessages[a].forEach(function (a) {
            this.handleMessage(a.payload)
        }.bind(this)), this.pendingMessages[a] = void 0)
    };
    b.prototype.loadCSS = function () {
        var a = document.createElement("link");
        a.href = (this.settings.gslbBaseURL ?
            this.settings.gslbBaseURL : this.settings.baseCoreURL) + "/embeddedservice/" + this.settings.releaseVersion + "/esw" + (this.settings.devMode ? "" : ".min") + ".css";
        a.type = "text/css";
        a.rel = "stylesheet";
        document.getElementsByTagName("head")[0].appendChild(a)
    };
    b.prototype.appendHelpButton = function (a) {
        var d = document.createElement("div"), b = "";
        d.className = "embeddedServiceHelpButton";
        this.isLanguageRtl(this.settings.language) && this.isDesktop() && (b = 'dir="rtl"');
        d.innerHTML = '<div class="helpButton"' + b + '><button class="helpButtonEnabled uiButton" href="javascript:void(0)"><span class="embeddedServiceIcon" aria-hidden="true" data-icon="&#59648;"></span><span class="helpButtonLabel" id="helpButtonSpan" aria-live="polite" aria-atomic="true"><span class="assistiveText">' +
            (this.settings.defaultAssistiveText || "") + '</span><span class="message"></span></span></button></div>';
        a || (d.style.display = "none");
        this.settings.targetElement.appendChild(d);
        this.setHelpButtonText(this.settings.defaultMinimizedText);
        "ontouchstart" in document.documentElement && [].slice.apply(document.querySelectorAll(".embeddedServiceHelpButton .uiButton")).forEach(function (a) {
            a.classList.add("no-hover")
        });
        this.onButtonStatusChange()
    };
    b.prototype.appendIFrame = function () {
        var a = document.createElement("iframe"),
            d = {};
        a.id = "esw_storage_iframe";
        a.src = this.settings.iframeURL;
        a.style.display = "none";
        a.onload = function () {
            var a = this.getESWFrame();
            this.isIframeReady = !0;
            this.outboundMessagesAwaitingIframeLoad.forEach(function (d) {
                a.postMessage(d, this.settings.iframeURL)
            }.bind(this));
            this.outboundMessagesAwaitingIframeLoad = [];
            this.iframeScriptsToLoad.forEach(function (a) {
                this.loadStorageScript(a)
            }.bind(this));
            d.deploymentId = this.settings.deploymentId;
            d.isSamePageNavigation = this.isSamePageNavigation();
            d.isRefresh = 1 ===
                window.performance.navigation.type;
            this.postMessage("session.updateStorage", d);
            this.iframeScriptsToLoad = []
        }.bind(this);
        this.settings.targetElement.appendChild(a);
        window.addEventListener("beforeunload", function (d) {
            this.isInternetExplorer() && (a.src = "about:blank");
            if (this.isMasterAndHasSlaves) if (embedded_svc.utils.fireEvent("snapinsCloseSessionWarning"), this.settings.closeSessionWarning && "function" === typeof this.settings.closeSessionWarning) this.settings.closeSessionWarning(); else return (d || window.event).returnValue =
                "You might lose the active chat session if you close this tab. Are you sure?";
            this.settings.__synchronous_decrement_tab || this.postMessage("chasitor.decrementActiveChatSession", this.settings.deploymentId)
        }.bind(this), !1)
    };
    b.prototype.preparePageForSidebar = function () {
        document.getElementById("snapins_invite") && embedded_svc.inviteAPI && embedded_svc.inviteAPI.inviteButton.setOnlineState(!1);
        embedded_svc.utils.fireEvent("beforeCreate");
        Object.keys(this.settings).forEach(function (a) {
        }.bind(this));
        this.mergeSettings(this.defaultSettings)
    };
    b.prototype.createLightningComponent = function (a) {
        this.preparePageForSidebar();
        this.createEmbeddedServiceComponent({attributes: {configurationData: this.settings, serializedSessionData: a}, locator: this.settings.targetElement}).then(function () {
            this.hideHelpButton();
            this.componentInitInProgress = !1;
            this.setHelpButtonText(this.settings.defaultMinimizedText);
            embedded_svc.utils.fireEvent("ready")
        }.bind(this))
    };
    b.prototype.loadLightningApp = function (a) {
        var d;
        if (this.settings.isExternalPage && "string" !== typeof this.settings.communityEndpointURL) throw Error("communityEndpointURL String property not set");
        if (d = document.getElementsByClassName("helpButton")[0]) {
            var b = d.getBoundingClientRect().width;
            0 < b && (d.style.width = b + "px")
        }
        this.setHelpButtonText(this.settings.loadingText, !1);
        this.instantiateLightningOutApplication({communityEndpointURL: this.settings.communityEndpointURL, oauthToken: this.auth.oauthToken}).then(this.createLightningComponent.bind(this, a))
    };
    b.prototype.initLightningOut = function (a) {
        this.hasSessionDataLoaded && ("function" !== typeof Promise ? this.loadScriptFromDirectory("common", "promisepolyfill",
            function () {
                this.initLightningOut(a)
            }.bind(this), !0) : this.loadLightningOutScripts().then(this.loadLightningApp.bind(this, a)))
    };
    b.prototype.setHelpButtonText = function (a, d) {
        var b = void 0 === this.settings.showIcon ? !0 : this.settings.showIcon;
        d = void 0 === d ? b : d;
        b = document.getElementById("helpButtonSpan");
        if (b) {
            var c = b.querySelector(".message");
            c.innerHTML = a;
            if (a = b.parentElement.querySelector(".embeddedServiceIcon")) a.style.display = d ? "inline-block" : "none"
        }
    };
    b.prototype.prepareDOM = function () {
        this.domInitInProgress ||
        (this.domInitInProgress = !0, this.appendIFrame())
    };
    b.prototype.addSessionHandlers = function () {
        this.addMessageHandler("session.onLoad", function () {
            this.postMessage("session.get", this.storageKeys)
        }.bind(this));
        this.addMessageHandler("session.sessionData", function (a) {
            this.resumeInitWithSessionData(a)
        }.bind(this));
        this.addMessageHandler("session.deletedSessionData", function (a) {
            -1 < a.indexOf("CHASITOR_SERIALIZED_KEY") && (this.loginPendingSerializedData = void 0)
        }.bind(this));
        this.addMessageHandler("session.updateMaster",
            function (a) {
                a && (a.isMaster ? sessionStorage.setItem(this.settings.storageDomain + "MASTER_DEPLOYMENT_ID", this.settings.deploymentId) : sessionStorage.removeItem(this.settings.storageDomain + "MASTER_DEPLOYMENT_ID"), this.isMasterAndHasSlaves = 1 < a.activeChatSessions && a.isMaster, embedded_svc && embedded_svc.liveAgentAPI && (embedded_svc.liveAgentAPI.browserSessionInfo = a))
            }.bind(this))
    };
    b.prototype.addMetaTag = function (a, d) {
        var b = document.createElement("meta");
        b.name = a;
        b.content = d;
        document.head.appendChild(b)
    };
    b.prototype.init =
        function (a, d, b, c, e, f) {
            this.settings.baseCoreURL = a;
            this.settings.communityEndpointURL = d;
            this.settings.gslbBaseURL = b ? b : a;
            this.settings.orgId = c;
            this.settings.releaseVersion = "5.0";
            this.settings.eswConfigDevName = e;
            this.settings.disableDeploymentDataPrefetch = !0;
            this.mergeSettings(f || {});
            this.adjustCommunityStorageDomain();
            if ("string" !== typeof this.settings.baseCoreURL) throw Error("Base core URL value must be a string.");
            if (!this.isOrganizationId(this.settings.orgId)) throw Error("Invalid OrganizationId Parameter Value: " +
                this.settings.orgId);
            embedded_svc.utils ? this.finishInit() : this.loadScriptFromDirectory("utils", "common", this.finishInit.bind(this))
        };
    b.prototype.finishInit = function () {
        this.storedEventHandlers && (Object.getOwnPropertyNames(this.storedEventHandlers).forEach(function (a) {
            this.storedEventHandlers[a].forEach(function (b) {
                embedded_svc.utils.addEventHandler(a, b)
            })
        }.bind(this)), this.storedEventHandlers = {});
        if (!embedded_svc.utils.fireEvent("validateInit", function (a) {
            return -1 !== a.indexOf(!1)
        }, this.settings)) {
            this.checkForNativeFunctionOverrides();
            this.settings.appendHelpButton && this.loadCSS();
            if (!this.settings.targetElement) throw Error("No targetElement specified");
            this.settings.iframeURL = this.settings.gslbBaseURL + "/embeddedservice/" + this.settings.releaseVersion + (this.settings.devMode ? "/eswDev.html" : "/esw.html") + "?parent=" + document.location.href;
            this.addSessionHandlers();
            this.loadFeatures(this.onFeatureScriptsLoaded.bind(this));
            embedded_svc.utils.fireEvent("afterInit", void 0, this.settings)
        }
    };
    b.prototype.onFeatureScriptsLoaded = function () {
        "complete" ===
        document.readyState ? setTimeout(this.prepareDOM.bind(this), 1) : document.addEventListener ? (document.addEventListener("DOMContentLoaded", this.prepareDOM.bind(this), !1), window.addEventListener("load", this.prepareDOM.bind(this), !1)) : window.attachEvent ? window.attachEvent("onload", this.prepareDOM.bind(this)) : this.log("No available event model. Exiting.")
    };
    b.prototype.checkForNativeFunctionOverrides = function () {
        [{
            name: "document",
            object: document,
            functions: "addEventListener createAttribute createComment createDocumentFragment createElementNS createTextNode createRange getElementById getElementsByTagName getElementsByClassName querySelector querySelectorAll removeEventListener".split(" ")
        },
            {name: "window", object: window, functions: "addEventListener clearTimeout dispatchEvent open removeEventListener requestAnimationFrame setInterval setTimeout".split(" ")}].forEach(function (a) {
            a.functions.forEach(function (b) {
                b in a.object && !this.isNativeFunction(a.object, b) && this.warning("Embedded Service Chat may not function correctly with this native JS function modified: " + a.name + "." + b, !0)
            }.bind(this))
        }.bind(this))
    };
    b.prototype.isNativeFunction = function (a, b) {
        return Function.prototype.toString.call(a[b]).match(/\[native code\]/)
    };
    b.prototype.onHelpButtonClick = function () {
        if (!this.componentInitInProgress && !document.getElementsByClassName("embeddedServiceSidebar").length) {
            this.componentInitInProgress = !0;
            try {
                this.checkAuthentication(), embedded_svc.utils.fireEvent("onHelpButtonClick")
            } catch (a) {
                throw this.componentInitInProgress = !1, a;
            }
        }
    };
    b.prototype.resumeInitWithSessionData = function (a) {
        var b = embedded_svc.utils.fireEvent("sessionDataRetrieved", function (a) {
            return -1 !== a.indexOf(!0)
        }, a), c = !1, e = !1;
        this.settings.linkAction.valid ? c = !0 :
            b ? (this.log("Existing session found. Continuing with data: " + a), e = c = !0, embedded_svc.menu && embedded_svc.menu.hideTopContainer()) : this.componentInitInProgress && (c = !0);
        this.hasSessionDataLoaded = !0;
        a.ESW_OAUTH_TOKEN && (this.auth.oauthToken = a.ESW_OAUTH_TOKEN);
        this.loginPendingSerializedData = e ? a : void 0;
        c && (this.componentInitInProgress = !0, this.checkAuthentication());
        this.settings.appendHelpButton && this.appendHelpButton(this.settings.displayHelpButton && !b)
    };
    b.prototype.checkAuthentication = function () {
        this.isAuthenticationRequired &&
        !this.settings.allowGuestUsers ? this.auth.oauthToken ? (this.loginButtonPressed || this.componentInitInProgress) && this.initLightningOut(this.loginPendingSerializedData) : embedded_svc.utils.fireEvent("requireauth") : (this.loginButtonPressed || this.componentInitInProgress) && this.initLightningOut(this.loginPendingSerializedData)
    };
    b.prototype.postMessage = function (a, b) {
        a = {domain: this.settings.storageDomain, data: b, method: a};
        (b = this.getESWFrame()) ? b.postMessage(a, this.settings.iframeURL) : this.outboundMessagesAwaitingIframeLoad.push(a)
    };
    b.prototype.setSessionData = function (a, b) {
        if ("object" === typeof a) var d = a; else d = {}, d[a] = b;
        this.postMessage("session.set", d)
    };
    b.prototype.deleteSessionData = function (a) {
        a = Array.isArray(a) ? a : [a];
        this.postMessage("session.delete", a)
    };
    b.prototype.defineFeature = function (a, b) {
        this.featureScripts[a] = b
    };
    b.prototype.registerStorageKeys = function (a) {
        "string" === typeof a ? this.storageKeys.push(a) : a.forEach(function (a) {
            this.storageKeys.push(a)
        }.bind(this))
    };
    b.prototype.addMessageHandler = function (a, b) {
        this.messageHandlers[a] &&
        this.warning("Replacing an existing handler for message type " + a);
        this.messageHandlers[a] = b
    };
    b.prototype.loadStorageScript = function (a) {
        this.isIframeReady ? this.postMessage("script.load", a) : this.iframeScriptsToLoad.push(a)
    };
    b.prototype.loadScriptFromDirectory = function (a, b, c, e) {
        b = b.toLowerCase();
        var d = document.createElement("script"), g = this.settings.gslbBaseURL;
        d.type = "text/javascript";
        d.src = [g, "embeddedservice", e ? void 0 : this.settings.releaseVersion, a, b + (this.settings.devMode ? "" : ".min") + ".js"].filter(function (a) {
            return !!a
        }).join("/");
        c && (d.onload = c);
        document.body.appendChild(d)
    };
    b.prototype.loadFeatures = function (a) {
        this.settings.enabledFeatures.forEach(function (b) {
            "base" !== b && -1 === this.availableFeatures.indexOf(b.toLowerCase()) && this.loadFeatureScript(b, a)
        }.bind(this))
    };
    b.prototype.addEventHandler = function (a, b) {
        window.embedded_svc && embedded_svc.utils ? embedded_svc.utils.addEventHandler(a, b) : (this.storedEventHandlers[a] || (this.storedEventHandlers[a] = []), this.storedEventHandlers[a].push(b))
    };
    b.prototype.setupMessageListener = function () {
        window.addEventListener("message",
            function (a) {
                var b = a.data, c = a.origin.split(":")[1].replace("//", "");
                b && b.method && embedded_svc.isMessageFromSalesforceDomain(c) && ("session.onLoad" === b.method && -1 === this.settings.iframeURL.indexOf(c) && (c = this.settings.iframeURL.split("/")[2], a = a.origin.split("/")[2], this.settings.iframeURL = this.settings.iframeURL.replace(c, a)), a = b.method.split(".")[0].toLowerCase(), -1 === this.availableFeatures.indexOf(a) ? (this.pendingMessages[a] || (this.pendingMessages[a] = []), this.pendingMessages[a].push({
                    direction: "incoming",
                    payload: b
                })) : this.handleMessage(b))
            }.bind(this), !1)
    };
    b.prototype.handleMessage = function (a) {
        if (this.messageHandlers[a.method]) this.messageHandlers[a.method](a.data); else this.log("Unregistered method " + a.method + " received.")
    };
    b.prototype.isMessageFromSalesforceDomain = function (a) {
        if (e() && a === document.domain) return !0;
        var b = function (a, b) {
            return -1 !== a.indexOf(b, a.length - b.length)
        };
        return f.some(function (d) {
            return b(a, d)
        })
    };
    b.prototype.isCommunityDomain = function (a) {
        return ".force.com" === a.substr(-10)
    };
    b.prototype.isSamePageNavigation =
        function () {
            var a = document.domain;
            if (this.isCommunityDomain(document.domain)) {
                var b = a + "/" + window.location.pathname.split("/")[1];
                b === this.settings.storageDomain && (a = b)
            }
            return a.substr(-this.settings.storageDomain.length) === this.settings.storageDomain
        };
    b.prototype.addDefaultSetting = function (a, b) {
        this.defaultSettings[a] = b
    };
    b.prototype.onButtonStatusChange = function () {
        var a = document.querySelector(".embeddedServiceHelpButton button"), b;
        if (embedded_svc.menu) embedded_svc.menu.onAgentAvailabilityChange();
        a && (b = a.querySelector(".message")) && (this.isButtonDisabled ? (a.onclick = function () {
        }, a.classList.remove("helpButtonEnabled"), a.classList.add("helpButtonDisabled"), b.innerHTML = this.settings.disabledMinimizedText) : (a.onclick = this.onHelpButtonClick.bind(this), a.classList.remove("helpButtonDisabled"), a.classList.add("helpButtonEnabled"), b.innerHTML = this.settings.defaultMinimizedText))
    };
    b.prototype.hideHelpButton = function () {
        var a = document.querySelector(".embeddedServiceHelpButton");
        a && (a.style.display = "none")
    };
    b.prototype.showHelpButton = function () {
        var a = document.querySelector(".embeddedServiceHelpButton");
        a && (a.style.display = "")
    };
    b.prototype.setDefaultButtonText = function (a, b, c, e) {
        this.settings.entryFeature === a && (this.settings.defaultMinimizedText = this.settings.defaultMinimizedText || b, this.settings.disabledMinimizedText = this.settings.disabledMinimizedText || c, this.settings.defaultAssistiveText = this.settings.defaultAssistiveText || e || "")
    };
    b.prototype.setDefaultShowIcon = function (a, b) {
        this.settings.entryFeature ===
        a && void 0 === this.settings.showIcon && (this.settings.showIcon = b)
    };
    b.prototype.registerLinkAction = function (a, b) {
        var d = this.settings.linkAction;
        this.validLinkActions[a] || (this.validLinkActions[a] = []);
        -1 === this.validLinkActions[a].indexOf(b) && this.validLinkActions[a].push(b);
        d.feature && d.name && d.feature.toLowerCase() === a.toLowerCase() && d.name.toLowerCase() === b.toLowerCase() && (d.valid = !0, d.feature = a, this.settings.entryFeature = a)
    };
    b.prototype.setLinkAction = function (a, b, c) {
        var d = Object.keys(this.validLinkActions).filter(function (b) {
            return b.toLowerCase() ===
                a.toLowerCase()
        })[0];
        d ? (this.settings.linkAction.feature = d, this.settings.linkAction.name = this.validLinkActions[d].filter(function (a) {
            return a.toLowerCase() === b.toLowerCase()
        })[0], this.settings.linkAction.valid = void 0 !== this.settings.linkAction.name, this.settings.linkActionParameters = c) : this.settings.linkAction.valid = !1
    };
    b.prototype.getLinkActionData = function () {
        window.location.search.replace(/([a-zA-Z0-9._]+)=([^&\s]+)/g, function (a, b, c) {
            a = b.toLowerCase();
            0 === a.indexOf("snapins.") && (a = a.replace("snapins.",
                ""), "action" === a ? (c = c.split("."), 2 === c.length && (this.settings.linkAction.feature = c[0], this.settings.linkAction.name = c[1])) : this.settings.linkActionParameters[a.toLowerCase()] = c)
        }.bind(this))
    };
    b.prototype.requireAuthentication = function () {
        var a = document.createElement("script"), b = document.createElement("style"), c = document.querySelector(this.settings.loginTargetQuerySelector);
        this.isAuthenticationRequired = !0;
        if ("https:" !== window.location.protocol && !this.settings.devMode) throw this.settings.displayHelpButton =
            !1, Error("Snap-in authentication requires HTTPS.");
        if (!this.settings.useCustomAuthentication) {
            if (!this.settings.loginClientId || !this.settings.loginRedirectURL || !this.settings.loginTargetQuerySelector) throw Error("Authentication in Snap-ins requires these valid settings params: loginClientId, loginRedirectURL, loginTargetQuerySelector.");
            if (c) this.loginButtonPressed = !1, c.addEventListener("click", function () {
                this.loginButtonPressed = !0
            }.bind(this)); else throw Error("loginTargetQuerySelector is not a valid DOM element.");
            this.addMetaTag("salesforce-community", this.settings.communityEndpointURL);
            this.addMetaTag("salesforce-client-id", this.settings.loginClientId);
            this.addMetaTag("salesforce-redirect-uri", this.settings.loginRedirectURL);
            this.addMetaTag("salesforce-mode", "popup");
            this.addMetaTag("salesforce-target", this.settings.loginTargetQuerySelector);
            this.addMetaTag("salesforce-login-handler", "__snapinsLoginCallback");
            this.addMetaTag("salesforce-logout-handler", "__snapinsLogoutCallback");
            embedded_svc.utils.addEventHandler("requireauth",
                function () {
                    var a = setInterval(function () {
                        window.SFIDWidget && (clearInterval(a), window.SFIDWidget.openid_response ? window.__snapinsLoginCallback() : window.SFIDWidget.login())
                    }, 100)
                });
            embedded_svc.utils.addEventHandler("autherror", function (a) {
                if (window.SFIDWidget) {
                    this.loginButtonPressed = !0;
                    window.SFIDWidget.logout();
                    var b = setInterval(function () {
                        window.SFIDWidget.config && (clearInterval(b), embedded_svc.utils.fireEvent("requireauth"))
                    }.bind(this, b), 100)
                }
            }.bind(this));
            window.__snapinsLoginCallback = function () {
                var a =
                    document.querySelector(this.settings.loginTargetQuerySelector), b = document.createElement("button");
                if (this.loginButtonPressed || this.componentInitInProgress) a.innerHTML = "";
                b.className = "authenticationStart";
                b.innerHTML = this.settings.authenticationStartLabel;
                b.addEventListener("click", this.onHelpButtonClick.bind(this));
                a.appendChild(b);
                this.auth.oauthToken = window.SFIDWidget.openid_response.access_token
            }.bind(this);
            window.__snapinsLogoutCallback = function () {
                this.auth.oauthToken = void 0;
                window.SFIDWidget.init()
            }.bind(this);
            document.head.appendChild(b);
            b.sheet.insertRule(".sfid-logout { display: none; }", 0);
            a.type = "text/javascript";
            a.src = this.settings.communityEndpointURL + "/servlet/servlet.loginwidgetcontroller?type=javascript_widget" + (embedded_svc.settings.devMode ? "&min=false" : "");
            document.head.appendChild(a)
        }
    };
    b.prototype.requireSLDS = function () {
        this.settings.requireSLDS = !0;
        if (this.settings.targetElement === document.body) {
            var a = document.createElement("div");
            a.id = "esw-snapin-target";
            document.body.appendChild(a);
            this.settings.targetElement =
                a
        }
        this.settings.targetElement.classList.add("slds-scope");
        a = document.createElement("link");
        a.href = (this.settings.gslbBaseURL ? this.settings.gslbBaseURL : this.settings.baseCoreURL) + "/embeddedservice/" + this.settings.releaseVersion + "/esw-slds" + (this.settings.devMode ? "" : ".min") + ".css";
        a.type = "text/css";
        a.rel = "stylesheet";
        document.getElementsByTagName("head")[0].appendChild(a)
    };
    b.prototype.validateHeaderValue = function (a) {
        return /^[0-9a-zA-Z!#$%&'*+-.^_`|~" ]*$/g.test(a)
    };
    b.prototype.isLanguageRtl = function (a) {
        if (a &&
            "" !== a.trim()) switch (a.substring(0, 2)) {
            case "ar":
            case "fa":
            case "he":
            case "iw":
            case "ji":
            case "ur":
            case "yi":
                return !0;
            default:
                return !1
        }
    };
    b.prototype.isDesktop = function () {
        return -1 === navigator.userAgent.indexOf("Mobi")
    };
    window.embedded_svc = new b;
    Object.getOwnPropertyNames(c).forEach(function (a) {
        var b = c[a];
        "object" === b ? (window.embedded_svc[a] = {}, Object.keys(b).forEach(function (c) {
            window.embedded_svc[a][c] = b[c]
        })) : window.embedded_svc[a] = b
    })
})(window.embedded_svc || {});