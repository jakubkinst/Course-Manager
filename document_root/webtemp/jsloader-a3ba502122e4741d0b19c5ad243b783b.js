// Rozšířit jQuery o texylování
jQuery.fn.extend({
	texyla: function (options) {
		this.filter("textarea").each(function () {
			new Texyla(this, options);
		});
	}
});

// $.texyla();
jQuery.extend({
	texyla: function (options) {
		jQuery("textarea").texyla(options);
	}
});

// Texyla konstruktor
function Texyla(textarea, options) {
	// uloží nastavení
	this.options = jQuery.extend({}, this.defaultOptions, options || {});
	
	// uložím jQuery objekt textarey
	this.textarea = jQuery(textarea);
	
	// ochrana proti vícenásobnému ztexylování
	if (this.textarea.data("texyla")) return false;
	this.textarea.data("texyla", true);
	
	// nastavím jazyk
	var lng = this.options.language;
	if (!this.languages[lng]) {
		this.error("Language '" + lng + "' is not loaded.");
		return false;
	}
	this.lng = this.languages[lng];
	
	// náhrada za %texyla_base% v adresách
	this.baseDir = this.options.baseDir || this.baseDir;
	this.options.iconPath = this.expand(this.options.iconPath);
	this.options.previewPath = this.expand(this.options.previewPath);
	
	// vytvořím texy pro texylu
	this.texy = new Texy(this);
	
	// obalit ovládacíma blbostma
	this.wrap();
	
	// spustí pluginy
	for (var i = 0; i < this.initPlugins.length; i++) {
		this.initPlugins[i].apply(this);
	}
};

// nahradí v řetězci hodnoty za proměnné
Texyla.prototype.expand = function (text, variable) {
	text = text.replace("%texyla_base%", this.baseDir);
	if (variable) {
		text = text.replace("%var%", variable);
	}
	
	return text;
};

// pole funkcí zprovozňující pluginy
Texyla.prototype.initPlugins = [];

// příkazy
jQuery.texyla.setDefaults = function (defaults) {
	jQuery.extend(Texyla.prototype.defaultOptions, defaults);
};

jQuery.texyla.initPlugin = function (pluginInit) {
	Texyla.prototype.initPlugins.push(pluginInit);
};

jQuery.texyla.addButton = function (name, func) {
	Texyla.prototype.buttons[name] = func;
};

jQuery.texyla.extend = function (extendingObject) {
	jQuery.extend(Texyla.prototype, extendingObject);
};

jQuery.texyla.addStrings = function (lng, strings) {
	if (!Texyla.prototype.languages[lng]) {
		Texyla.prototype.languages[lng] = {};
	}

	jQuery.extend(Texyla.prototype.languages[lng], strings);
};

jQuery.texyla.setErrorHandler = function (handler) {
	Texyla.prototype.error = handler;
};

// Odeslat formulář
Texyla.prototype.submit = function () {
	var f = this.textarea.get(0).form;
	function submitnout() {
		if (f.submit.tagName == undefined) {
			f.submit();
		} else {
			f.submit.click();
		}
	}
	if (typeof f.onsubmit == 'function') {
		if (f.onsubmit()) {
			submitnout();
		}
	} else {
		submitnout();
	}
};

// chybový handler
Texyla.prototype.error = function (message) {
	alert("Error: " + message);
};

// výchozí adresář
Texyla.prototype.baseDir = jQuery("head script:last").attr("src").replace(/(\/js)?\/?[\w-]+\.js$/, '');

// jazyky
Texyla.prototype.languages = {};

// výchozí nastavení
Texyla.prototype.defaultOptions = {
	// šířka Texyly v pixelech
	width: null,
	// Odsazení textarey od krajů Texyly
	padding: 5,
	// výchozí konfigurace Texy: žádná
	texyCfg: "",
	// lišta
	toolbar: ['bold', 'italic', null, 'ul', 'ol', null, 'link', null, 'emoticon', 'symbol', "img", "table", null, ['web']],
	// tlačítka vlevo dole
	bottomLeftToolbar: ['edit', 'preview'],
	// tlačítka vpravo dole při editaci
	bottomRightEditToolbar: ['syntax'],
	// tlačítka vpravo dole při náhledu
	bottomRightPreviewToolbar: ['submit'],
	// typ tlačítek (span | button)
	buttonType: "span",
	// jestli bude levá dolní lišta zobrazena jako taby
	tabs: false,
	// výchozí pohled
	defaultView: "edit",
	// šířka ikon
	iconWidth: 16,
	// výška ikon
	iconHeight: 16,
	// adresář Texyly. Texyla se to pokusí zjistit automaticky, ale je to vhodné vyplnit.
	baseDir: null,
	// cesta k ikonkám
	iconPath: "%texyla_base%/icons/%var%.png",
	// cesta k náhledu
	previewPath: null,
	// jazyk
	language: "cs"
};/**
 *	Selection
 *	obsluhuje výběr v textaree
 *	@author Jan Marek
 */
function Selection(ta) {
	this.textarea = ta;
};

Selection.prototype = {
	
	// oddělovač řádků
	lineFeedFormat: null,
	
	// jestli jsme si jisti s formátem oddělovače řádků
	lineFeedKnown: false,
	
	/**
	 *	Zjišťuje, zdali je prohlížeč internet explorer
	 */
	isIe: function () {
		// Opera, Firefox
		if (this.textarea.selectionStart || this.textarea.selectionStart === 0) {
			return false;
			
		// IE
		} else if (document.selection) {
			return true;
		}
		
		return null;
	},
	
	// obalí výběr (firstTexy + výběr + secondText)
	tag: function (firstText, secondText) {
		this.update();
		
		this.changeSelection(firstText + this.text() + secondText);
		
		// je li obalen kurzor
		if (this.isCursor()) {
			this.select(this.start + firstText.length, 0);
			
		// či výběr
		} else {
			this.select(this.start, firstText.length + this.length() + secondText.length);
		}
		
		
	},
	
	// nahradí výběr proměnnou replacement
	replace: function (replacement) {
		if (replacement === null) return;
		
		this.update();
		
		this.changeSelection(replacement);
		
		this.select(this.start, replacement.length);
	},
	
	// odstraní případnou jednu mezeru vpravo z výběru
	trimSelect: function () {
		this.update();

		if (this.text().substring(this.length(), this.length() - 1) == " ") {
			this.select(this.start, this.length() - 1);
		}
		
		return this.update();
	},
	
	// odstraní případnou jednu mezeru vpravo z výběru a zavolá funkci this.tag()
	// FF & IE fix (po dvojkliku na slovo vybere i mezeru za ním)
	phrase: function (firstText, secondText) {
		this.trimSelect().tag(firstText, secondText ? secondText : firstText);
	},
	
	// změna výběru
	changeSelection: function (replacement) {		
		// Kolik je odrolováno
		var scrolled = this.textarea.scrollTop;
		
		// Změna textu v textaree
		var val = this.textarea.value;
		this.textarea.value = val.substring(0, this.start) + replacement + val.substring(this.end);
		
		// Odrolovat na původní pozici
		this.textarea.scrollTop = scrolled;
	},

	// Funkce zjistí pravděpodobnou podobu formátu nového řádku.
	lf: function() {
		if (this.lineFeedKnown) return this.lineFeedFormat;
		
		// Pokusí se ho nalézt:
		var unix = this.textarea.value.indexOf('\n');
		var mac = this.textarea.value.indexOf('\r');
		var win = this.textarea.value.indexOf('\r\n');
		
		var lineFeed = null;
		if (unix >= 0) lineFeed = '\n';
		if (mac >= 0) lineFeed = '\r';
		if (win >= 0) lineFeed = '\r\n';
		
		// V případě úspěchu nastaví proměnnou this.lineFeedKnown na true a funkce již později hledání neopakuje.
		if (lineFeed) {
			this.lineFeedFormat = lineFeed;
			this.lineFeedKnown = true;
			return lineFeed;
		}
		
		// Jinak se nový řádek vrátí provizorně podle prohlížeče. (O, IE -> win, FF -> unix)
		return document.selection ? '\r\n' : '\n';
	},
	
	// Ulož vlastnosti výběru
	update: function() {
		this.textarea.focus();
		
		// IE
		if (this.isIe()) {
			// Copyright (c) 2005-2007 KOSEKI Kengo
			var range = document.selection.createRange();
			var bookmark = range.getBookmark();

			var contents = this.textarea.value;
			var originalContents = contents;
			var marker = "[~M~A~R~K~E~R~]";
			while (contents.indexOf(marker) != -1) {
				marker = marker + Math.random();
			}

			range.text = marker + range.text + marker;
			contents = this.textarea.value;

			this.start = contents.indexOf(marker);
			contents = contents.replace(marker, "");
			this.end = contents.indexOf(marker);

			this.textarea.value = originalContents;
			range.moveToBookmark(bookmark);
			range.select();
		// O, FF
		} else { 
			this.start = this.textarea.selectionStart;
			this.end = this.textarea.selectionEnd;
		}
		
		return this;
	},

	length: function () {
		return this.end - this.start;
	},

	text: function () {
		return this.textarea.value.substring(this.start, this.end);
	},

	isCursor: function () {
		return this.start == this.end;
	},
	
	// vybere od pozice from text o délce length
	select: function(from, length) {
		if (this.isIe()) {
			var lfCount = this.textarea.value.substring(0, from).split("\r\n").length - 1;
			from -= lfCount;
			this.textarea.focus();
			this.textarea.select();
			var ieSelected = document.selection.createRange();
			ieSelected.collapse(true);
			ieSelected.moveStart("character", from);
			ieSelected.moveEnd("character", length);
			ieSelected.select();
		} else {
			this.textarea.selectionStart = from;
			this.textarea.selectionEnd = from + length;
		}
		
		this.textarea.focus();
	},
	
	// vybrat blok
	selectBlock: function() {
		this.update();
		
		var lf = this.lf();
		var ta = this.textarea;
		
		// začátek
		var workFrom = ta.value.substring(0, this.start).lastIndexOf(lf);
		if (workFrom !== -1) workFrom += lf.length;
		var from = Math.max(0, workFrom);
		
		// konec
		var len = ta.value.substring(from, this.start).length + this.length();
		var fromSelectionEnd = ta.value.substring(this.end, ta.value.length);
		var lineFeedPos = fromSelectionEnd.indexOf(lf);
		len += lineFeedPos == -1 ? fromSelectionEnd.length : lineFeedPos;
		
		this.select(from, len);
		return this.update();
	}
};function Texy(texyla) {
	this.textarea = texyla.textarea.get(0);
	this.texyla = texyla;
};

// class Texy extends Selection
Texy.prototype = jQuery.extend({}, Selection.prototype, {

	// tučné písmo
	bold: function () {
		this.trimSelect();

		var text = this.text();

		if (text.match(/^\*\*.*\*\*$/)) {
			this.replace(text.substring(2, text.length - 2));
		} else {
			this.tag("**", "**");
		}
	},

	// kurzíva
	italic: function () {
		this.trimSelect();

		var text = this.text();

		if (text.match(/^\*\*\*.*\*\*\*$/) || text.match(/^\*[^*]+\*$/)) {
			this.replace(text.substring(1, text.length - 1));
		} else {
			this.tag("*", "*");
		}
	},

	// blok
	block: function(what) {
		this.tag('/--' + what + this.lf(), this.lf() + '\\--');
	},
	
	// odkaz
	link: function(addr) {
		if (addr) this.phrase('"', '":' + addr);
	},
	
	// acronym
	acronym: function(title) {
		this.update();
		if (title) {
			// Nejsou potřeba uvozovky. př.: slovo((titulek))
			if (this.text().match(/^[a-zA-ZěščřžýáíéúůĚŠČŘŽÝÁÍÉÚŮ]{2,}$/)) {
				this.tag('','((' + title + '))');
				
			// Jsou potřeba uvozovky. př.: "třeba dvě slova"((titulek))
			} else {
				this.phrase('"', '"((' + title + '))');
			}
		}
	},
	
	// čára
	line: function() {
		this.update();
		var lf = this.lf();
		
		// text
		var lineText = lf + lf + '-------------------' + lf + lf;
		
		// vložit
		if (this.isCursor()) this.tag(lineText, ''); else  this.replace(lineText);
	},

	// zarovnání
	align: function(type) {
		this.update();
		var lf = this.lf();
		
		var start = '.' + type + lf;
		var newPar = lf + lf;
		var found = this.textarea.value.substring(0, this.start).lastIndexOf(newPar);
		var beforePar = found + newPar.length;
		
		if (found ==- 1) {
			this.textarea.value = start + this.textarea.value;
		} else {
			this.textarea.value = this.textarea.value.substring(0, beforePar) + start + this.textarea.value.substring(beforePar);
		}
		this.select(this.start + start.length, this.length());
	},

	// original: Dougie Lawson, http://web.ukonline.co.uk/dougie.lawson/
	_toRoman: function(num) {
		num = Math.min(parseInt(num, 10), 5999);
		var mill = [ '', 'M', 'MM', 'MMM', 'MMMM', 'MMMMM' ],
		cent = [ '', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM' ],
		tens = [ '', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC' ],
		ones = [ '', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX' ],
		m, c, t, r = function(n) {
			n = (num - (num % n)) / n;
			return n;
		};
		m = r(1000);
		num = num % 1000;
		c = r(100);
		num = num % 100;
		t = r(10);
		return mill[m] + cent[c] + tens[t] + ones[num % 10];
	},

	_toLetter: function(n) {
		var alphabet = [
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m",
			"n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
		];
		return alphabet[Math.max(0, Math.min(n, alphabet.length) - 1)];
	},

	// vytvoří seznam - číslovaný (type == "ol"), s odrážkami (type == "ul"), blockquote (type == "bq")
	list: function(type) {
		this.selectBlock();
		var lf = this.lf();
		var lines = this.text().split(lf);
		var lineCt = this.isCursor() ? 3 : lines.length;
		var replacement = '';
		
		for (var i = 1; i <= lineCt; i++) {
			var bullet = {
				ul: '-',
				ol: i + ')',
				bq: '>',
				indent: '',
				romans: this._toRoman(i) + ')',
				smallRomans: this._toRoman(i).toLowerCase() + ')',
				smallAlphabet: this._toLetter(i) + ')',
				bigAlphabet: this._toLetter(i).toUpperCase() + ')'
			};
			
			replacement += bullet[type] + ' ' + (!this.isCursor() ? lines[i - 1] : '') + (i != lineCt ? lf : '');
			
			// seznam okolo kurzoru - pozice kurzoru
			if (this.isCursor() && i === 1)  var curPos = replacement.length - 1;
		}
		
		if (this.isCursor()) {
			this.tag(replacement.substring(0, curPos), replacement.substring(curPos));
		} else {
			this.replace(replacement);
		}
	},

	// odsazení o mezeru
	indent: function () {
		this.list("indent");
	},

	// zrušit odsazení
	unindent: function () {
		this.selectBlock();
		var lines = this.text().split(this.lf());
		var replacement = [];

		for (var i = 0; i < lines.length; i++) {
			var first = lines[i].substring(0, 1);
			if (first == " " || first == "\t") {
				replacement.push(lines[i].substring(1, lines[i].length));
			} else {
				replacement.push(lines[i]);
			}
		}

		this.replace(replacement.join(this.lf()));
	},
	
	// vytvoří nadpis, podtrhne podle type
	heading: function(type) {
		this.selectBlock();
		var lf = this.lf();
		
		// podtržení
		function underline(len, type) {
			var txt = '';
			for (var i = 0; i < Math.max(3, len); i++) {
				txt += type;
			}
			
			return txt;
		}

		// Nový nadpis
		if (this.isCursor()) {
			var headingText = prompt(this.texyla.lng.texyHeadingText, '');
			if (headingText) {
				this.tag(headingText + lf + underline(headingText.length, type) + lf, '');
			}
			
		// Vyrobí nadpis z výběru
		} else {
			this.tag('', lf + underline(this.length(), type));
		}
	},
	
	// obrázek
	img: function(src, alt, align, descr) {		
		// Zarovnání na střed
		var imgT = '';
		
		if (align == '<>') {
			imgT += this.lf() + '.<>' + this.lf();
			align = false;
		}
		
		// Začátek
		imgT += '[* ' + src + ' ';
		
		// Popis
		imgT += alt ? '.('+ alt +') ' : '';
		
		// Zarovnání
		imgT += (align ? align : '*') + ']';
		
		// Popisek
		imgT += descr ? ' *** ' + alt : '';
		
		this.replace(imgT);
	},
	
	// tabulka
	table: function(cols, rows, header) {
		var lf = this.lf();
		var tabTxt = lf;
		
		for (var i = 0; i < rows; i++) {
			// Hlavička nahoře
			if (header === 'n' && i < 2) {
				tabTxt += '|';
				for (var j = 0; j < cols; ++j) {
					tabTxt += '--------';
				}
				tabTxt += lf;
			}
			
			// Buňky
			for (j = 0; j < cols; j++) {
				// Hlavička vlevo
				if (header === 'l' && j === 0) {
					tabTxt += "|* \t";
					
				// Buňka bez hlavičky
				} else {
					tabTxt += "| \t"; 
				}
				
				// pozice kurzoru
				if (i === 0 && j === 0) var curPos = tabTxt.length - 1;
			}
			tabTxt += '|' + lf;
		}
		tabTxt += lf;
		
		// Vloží tabulku
		this.tag(tabTxt.substring(0, curPos), tabTxt.substring(curPos));
	}
});// Funkce tlačítek
Texyla.prototype.buttons = {
	h1: function() {this.texy.heading('#');},
	h2: function() {this.texy.heading('*');},
	h3: function() {this.texy.heading('=');},
	h4: function() {this.texy.heading('-');},
	bold: function() {this.texy.bold();},
	italic: function() {this.texy.italic();},
	del: function() {this.texy.phrase('--');},
	center: function() {this.texy.align('<>');},
	left: function() {this.texy.align('<');},
	right: function() {this.texy.align('>');},
	justify: function() {this.texy.align('=');},
	ul: function() {this.texy.list('ul');},
	ol: function() {this.texy.list('ol');},
	olRomans: function() {this.texy.list('romans');},
	olRomansSmall: function() {this.texy.list('smallRomans');},
	olAlphabetSmall: function() {this.texy.list('smallAlphabet');},
	olAlphabetBig: function() {this.texy.list('bigAlphabet');},
	blockquote: function() {this.texy.list('bq');},
	indent: function() {this.texy.indent();},
	unindent: function() {this.texy.unindent();},
	sub: function() {this.texy.phrase('__');},
	sup: function() {this.texy.phrase('^^');},
	link: function() {this.texy.link(prompt(this.lng.linkUrl, 'http://'));},
	acronym: function() {this.texy.acronym(prompt(this.lng.acronymTitle, ''));},
	hr: function() {this.texy.line();},
	code: function() {this.texy.block('code');},
	codeHtml: function() {this.texy.block('code html');},
	codeCss: function() {this.texy.block('code css');},
	codeJs: function() {this.texy.block('code js');},
	codePhp: function() {this.texy.block('code php');},
	codeSql: function() {this.texy.block('code sql');},
	codeInline: function() {this.texy.phrase('`');},
	html: function() {this.texy.block('html');},
	notexy: function() {this.texy.phrase("''", "''");},
	web: function() {window.open('http://texyla.janmarek.net/');},
	syntax: function() {window.open(this.lng.syntaxUrl);},
	div: function() {this.texy.block('div');},
	comment: function() {this.texy.block('comment');},
	text: function() {this.texy.block('text');},
	preview: function () {this.view("preview");},
	htmlPreview: function () {this.view("htmlPreview");},
	edit: function () {this.view("edit");},
	submit: function () {this.submit();}
};
/**
 *	Obalit textareu ovládacíma věcma
 */
Texyla.prototype.wrap = function () {
	/* kontejner */
	this.container = this.textarea.wrap('<div class="texyla"></div>').parent();

	var containerWidth = this.options.width || this.textarea.get(0).offsetWidth || this.textarea.width();
	this.container.width(containerWidth);

	/* div s textareou */
	this.editDiv = this.textarea.wrap('<div class="textarea-container"></div>')
		.parent().wrap('<div class="edit-div"></div>').parent();

	// nastavím šířku
	if (this.textarea.get(0).offsetWidth > 0) { // viditelná textarea
		this.textarea.width(containerWidth);
		var delta = this.textarea.get(0).offsetWidth - containerWidth;
	} else {
		var delta = 0;
	}

	this.textarea.width(containerWidth - delta - 2 * this.options.padding);

	// uložit výšku textarey
	this.textareaHeight = this.textarea.get(0).offsetHeight;

	/* div s náhledem */
	this.previewDiv = jQuery('<div class="preview-div"></div>').insertAfter(this.editDiv);
	// hlavička
	this.previewDiv.prepend(
		'<div class="view-header" style="background-image: url(\'' +
		this.expand(this.options.iconPath, "preview") + '\');">' +
		this.lng.btn_preview + '</div>'
	);

	this.preview = jQuery('<div class="preview"></div>')
		.appendTo(this.previewDiv)
		.wrap('<div class="preview-wrapper ui-widget-content"></div>');

	/* div s html náhledem */
	this.htmlPreviewDiv = jQuery('<div class="html-preview-div"></div>').insertAfter(this.previewDiv);
	// hlavička
	this.htmlPreviewDiv.prepend(
		'<div class="view-header" ' +
		'style="background-image: url(\'' + this.expand(this.options.iconPath, "htmlPreview") + '\');">' +
		this.lng.btn_htmlPreview + '</div>'
	);

	this.htmlPreview = jQuery('<pre class="html-preview"></pre>')
		.appendTo(this.htmlPreviewDiv)
		.wrap('<div class="preview-wrapper ui-widget-content"></div>');


	// čekejte
	this.wait = jQuery('<div class="preview-wait">' + this.lng.wait + '</div>');

	// vyrobím tlačítka
	this.createToolbar();
	this.createBottomToolbar();

	// zapnu pohled
	this.view(this.options.defaultView, true);
};

/**
 *	vyrobí horní lištu
 */
Texyla.prototype.createToolbar = function () {
	// lišta
	var toolbar = jQuery('<ul class="toolbar"></ul>').prependTo(this.editDiv);
	var item, toolbar2;

	// prochází lištu
	for (var i = 0; i < this.options.toolbar.length; i++) {
		// aktuální položka
		item = this.options.toolbar[i];

		// tlačítko
		if (typeof item == "string") {
			jQuery(
				"<span title='" + this.lng["btn_" + item] + "'>" +
				"<img src='" + this.expand(this.options.iconPath, item) +
				"' width='" + this.options.iconWidth + "' height='" + this.options.iconHeight + "'>" +
				"</span>"
			)
				.click(this.clickButton(item))
				.appendTo("<li class='btn_" + item + "'></li>").parent()
				.appendTo(toolbar);
		}

		// separator
		else if (item === null) {
			toolbar.append("<li class='separator'></li>");
		}

		// podmenu
		else if (jQuery.isArray(item)) {
			toolbar2 = jQuery("<ul class='ui-widget-content ui-corner-all'></ul>");
			var menuTimeout;
			toolbar2.appendTo("<li class='menu'></li>").parent().mouseover(function () {
				// prevence proti zmizení
				clearTimeout(menuTimeout);
				// schovat ostatní menu
				jQuery(this).siblings().find("ul:visible").fadeOut("fast");
				// zobrazit
				jQuery(this).find("ul").show();
			}).mouseout(function () {
				// po chvíli zmizí
				var _this = this;
				menuTimeout = setTimeout(function () {
					jQuery(_this).find("ul").fadeOut("fast");
				}, 300);
			}).appendTo(toolbar);

			// jednotlivé položky v menu
			for (var j = 0; j < item.length; j++) {
				jQuery(
					"<li class='btn_" + item[j] + " ui-corner-all'>" +
					"<span style='background-image: url(\"" + this.expand(this.options.iconPath, item[j]) + "\");'>" +
					this.lng["btn_" + item[j]] + "</span></li>"
				)
					.hover(function () {
						jQuery(this).addClass("ui-state-hover");
					}, function () {
						jQuery(this).removeClass("ui-state-hover");
					})
					.click(this.clickButton(item[j]))
					.appendTo(toolbar2);
			}

		// label
		} else if (typeof(item) == "object" && item.type == "label") {
			var text = item.translatedText ? this.lng[item.translatedText] : item.text;
			toolbar.append("<li class='label ui-state-disabled'>" + text + "</li>");
		}
	}
};

/**
 *	Vrátí funkci, která se přiřadí tlačítkům po kliknutí
 *	@param string name název funkce
 *	@return function
 */
Texyla.prototype.clickButton = function (name) {
	var _this = this;

	if (name in this.buttons) {
		return function (e) {
			_this.buttons[name].call(_this, e);
		};
	} else {
		return function () {
			_this.error('Function "' + name + '" is not supported!');
		};
	}
};

/**
 *	Vyrobí spodní lišty
 */
Texyla.prototype.createBottomToolbar = function () {
	// vytvořit lišty
	var bottomToolbar = jQuery("<div class='bottom-toolbar'></div>").appendTo(this.container);
	this.leftToolbar = jQuery("<div class='left-toolbar'></div>").appendTo(bottomToolbar);
	var right = jQuery('<div class="right-toolbar"></div>').appendTo(bottomToolbar);
	this.rightEditToolbar = jQuery("<div class='right-edit-toolbar'></div>").appendTo(right);
	this.rightPreviewToolbar = jQuery("<div class='right-preview-toolbar'></div>").appendTo(right);

	// přidat CSS třídy

	// když to jsou taby
	if (this.options.tabs) {
		this.leftToolbar.addClass("tabs");
	// nebo jsou tlačítka typu span
	} else if (this.options.buttonType == "span") {
		this.leftToolbar.addClass("span-tb");
	}

	// pravá lišta s tlačítkami typu span?
	if (this.options.buttonType == "span") {
		right.addClass("span-tb");
	}

	// přidat tlačítka
	var _this = this;

	// vyrobit tlačítko
	function createButton(icon, name, func, tabs) {
		var iconUrl = _this.expand(_this.options.iconPath, icon);

		// tlačítko typu span
		if (_this.options.buttonType == "span" || tabs) {
			return jQuery(
				"<span class='btn btn_" + icon + " ui-state-default " + (tabs ? "ui-corner-bottom" : "ui-corner-all") + "'>" +
					"<span class='btn-left'></span><span class='btn-middle'>" +
					"<span style='background-image: url(\"" +  iconUrl + "\");' class='icon-span'>" + name + "</span>" +
					"</span><span class='btn-right'></span>" +
				"</span>"
			).click(func).hover(function () {
					jQuery(this).addClass("ui-state-hover");
			}, function () {
					jQuery(this).removeClass("ui-state-hover");
			});

		// klasické tlačítko
		} else {
			return jQuery(
				"<button type='button' class='btn_" + icon + "'>" +
				"<img src='" + iconUrl + "' width='" + _this.options.iconWidth + "' height='" + _this.options.iconHeight + "'>" +
				" " + name + "</button>"
			).click(func);
		}
	};

	// vyplnit lištu tlačítky
	function insertButtons(toolbar, buttons, tabs) {
		for (var i = 0; i < buttons.length; i++) {
			createButton(
				buttons[i],
				_this.lng["btn_" + buttons[i]],
				_this.clickButton(buttons[i]),
				tabs
			).appendTo(toolbar);
		}
	};

	// vložit tlačítka
	insertButtons(this.leftToolbar, this.options.bottomLeftToolbar, this.options.tabs);
	insertButtons(this.rightEditToolbar, this.options.bottomRightEditToolbar);
	insertButtons(this.rightPreviewToolbar, this.options.bottomRightPreviewToolbar);
};// Změnit pohled
Texyla.prototype.view = function(type, first) {
	// textarea value
	var taVal = this.textarea.val();
	
	// prázdná textarea
	if (type != "edit" && taVal == "") {
		// poprvé nebuzerovat a bez keců přepnout
		if (first) {
			this.view("edit");
			return;
		}
	
		alert(this.lng.viewEmpty);
		this.textarea.focus();
		
		return;
	}
	
	// schovávání a odkrývání
	switch (type) {
		// náhled
		case "preview":
			this.previewDiv.show();
			this.htmlPreviewDiv.hide();
			this.editDiv.hide();
			this.rightPreviewToolbar.show();
			this.rightEditToolbar.hide();
		break;
		
		// html náhled
		case "htmlPreview":
			this.previewDiv.hide();
			this.htmlPreviewDiv.show();
			this.editDiv.hide();
			this.rightPreviewToolbar.show();
			this.rightEditToolbar.hide();
		break;
		
		// upravovat
		case "edit":
			this.previewDiv.hide();
			this.htmlPreviewDiv.hide();
			this.editDiv.show();
			this.rightPreviewToolbar.hide();
			this.rightEditToolbar.show();
		break;
	}
	
	// výška náhledů
	if (type != "edit") {
		var height = this.textarea.get(0).offsetHeight || this.textareaHeight;
		if (height) {
			var curPrev = this[type == "preview" ? "preview" : "htmlPreview"].parent();
			curPrev.height(height);
			var delta = curPrev.get(0).offsetHeight - height;
			this.container.find("div.preview-wrapper").height(height - delta);
		} else {
			this.container.find("div.preview-wrapper").height("auto");
		}
	}
	
	// zvýraznění aktivního tabu
	if (this.options.tabs) {
		var tabs = this.leftToolbar;
		tabs.find(".ui-state-active").removeClass("ui-state-active");
		tabs.find(".btn_" + type).addClass("ui-state-active");
		
	// schovávání tlačítka aktivního pohledu
	} else {
		var views = ["preview", "htmlPreview", "edit"];
		for (var i = 0; i < views.length; i++) {
			if (views[i] == type) {
				this.container.find(".btn_" + type).hide();
			} else {
				this.container.find(".btn_" + views[i]).show();
			}
		}
	}

	// načtení náhledu
	if (type != "edit" && this.lastPreviewedTexy != taVal) {	
		// při načtení náhledu
		var _this = this;
		function onLoad(data) {
			// náhled
			_this.preview.html(data).show();
			
			// náhled html
			_this.htmlPreview.text(data.replace(new RegExp("\n", "g"), _this.texy.lf())).show();

			// obarvit html pomocí JUSHe
			if (typeof jush != 'undefined') {
				_this.htmlPreview.html(jush.highlight("htm", data));
			}
			
			// schovat čekejte
			_this.wait.hide();
		};
	
		// kešuje poslední texy
		this.lastPreviewedTexy = taVal;
		
		// zobrazí prosím čekejte
		var parent = this[type == "preview" ? "preview" : "htmlPreview"].parent();
		
		parent.prepend(this.wait);
		
		this.wait.show().css({
			marginTop: (parent.get(0).offsetHeight - this.wait.get(0).offsetHeight) / 2,
			marginLeft: (parent.get(0).offsetWidth - this.wait.get(0).offsetWidth) / 2
		});
		
		// a schová staré obsahy náhledů
		this.preview.hide();
		this.htmlPreview.hide();
		
		// volá ajax
		jQuery.post(this.options.previewPath, {texy: taVal, cfg: this.options.texyCfg}, onLoad, "html");
	}
};/**
 * Ajax upload plugin
 * Odešle formulář a zavolá callback s JSON daty v parametru
 */

jQuery.fn.extend({
	ajaxUpload: function(callback) {
		if (!this.is("form")) return;

		// počítadlo
		if (!arguments.callee.count) {
			arguments.callee.count = 0;
		}
		var target = "ajaxUploadFrame" + (++arguments.callee.count);

		// vyrobí rámec
		var iframe = jQuery(
			'<iframe src="" width="1" height="1" frameborder="0" ' +
			'name="' + target + '"></iframe>'
		);
		iframe.css({
			visibility: "hidden",
			position: "absolute",
			left: "-1000px",
			top: "-1000px"
		});
		iframe.appendTo("body");

		// po načtení stránky zpracuje požadavek
		iframe.load(function () {
			jQuery.event.trigger("ajaxComplete");

			var iframeEl = iframe.get(0);
			var body;
			if (iframeEl.contentDocument) {
				body = iframeEl.contentDocument.body;
			} else {
				body = iframeEl.contentWindow.document.body;
			}
			var content = jQuery(body).text();
			
			if (!content) {
				callback();
			} else {
				eval("var data = " + content + ";");
				callback(data);
			}

			// nechat zmizet iframe
			setTimeout(function () {
				iframe.remove()
			}, 1000);
		});

		// odešle formulář do rámce
		this.attr({
			target: target,
			method: "post",
			enctype: "multipart/form-data"
		}).submit();
		
		jQuery.event.trigger("ajaxStart");
	}
});jQuery.texyla.initPlugin(function () {
	// seznam otevřených oken
	// 		název: jQuery objekt
	this.openedWindows = {};
});

/**
 *	Přidat okno
 *	bude možné zavolat $.texyla.addWindow({title: "Okno", ...});
 */
jQuery.texyla.addWindow = function (name, options) {
	Texyla.prototype.windowConfigs[name] = options;
	
	// nastavit velikosti okna
	if (options.dimensions) {
		var defaults = {};
		defaults[name + "WindowDimensions"] = options.dimensions;
		jQuery.texyla.setDefaults(defaults);
	}

	// přidat tlačítko
	jQuery.texyla.addButton(name, function () {
		this.openWindow(name);
	});
};

jQuery.texyla.extend({
	// možná okna
	windowConfigs: {},

	openWindow: function (name) {
		// kontrola
		if (typeof(jQuery.fn.dialog) != "function") {
			this.error("jQuery UI plugin Dialog is not loaded.");
			return false;
		}

		if (!Texyla.prototype.windowConfigs[name]) {
			this.error("Window " + name + " is not defined.");
			return false;
		}

		// focusovat otevřené
		if (this.isWindowOpened(name)) {
			return this.getWindow(name).dialog("moveToTop");
		}

		// otevřít nové		
		var config = Texyla.prototype.windowConfigs[name];
		var el = config.createContent.call(this);

		// přiřadit do otevřených oken
		this.openedWindows[name] = el;
		
		// nastavení dialogu
		var options = config.options || {};

		// titulek
		options.title = config.title ? config.title : this.lng["win_" + name];
		
		// rozměry
		var dimensions = this.options[name + "WindowDimensions"];
		if (dimensions) {
			options.width = dimensions[0];
			options.height = dimensions[1];
		}

		// tlačítka
		var _this = this;
		if (config.action) {
			options.buttons = {};

			// tlačítko OK
			options.buttons[this.lng.windowOk] = function () {
				config.action.call(_this, el);
				if (!config.stayOpened) {
					_this.closeWindow(name);
				}
			};

			// tlačítko Storno
			options.buttons[this.lng.windowCancel] = function () {
				_this.closeWindow(name);
			};
		}

		// zavření
		options.close = function () {
			_this.closeWindow(name);
		};

		// vytvořit dialog
		el.dialog(options);

		// focus na první input
		el.find("input:first").focus();

		return el;
	},

	closeWindow: function (name) {
		// zrušení objektu v domu
		this.openedWindows[name].dialog("destroy").remove();
		// vynulování
		this.openedWindows[name] = null;
	},

	isWindowOpened: function (name) {
		return this.openedWindows[name] ? true : false;
	},

	getWindowAction: function (name) {
		return Texyla.prototype.windowConfigs[name].action;
	},
	
	/**
	 *	Získat objekt okna
	 *	@param string name jméno okna
	 *	@return jQuery|null
	 */
	getWindow: function (name) {
		return this.openedWindows[name] ? this.openedWindows[name] : null;
	}
});/**
 * English translation
 */
jQuery.texyla.addStrings("en", {
	// buttons
	btn_h1: "The biggest heading",
	btn_h2: "Big heading",
	btn_h3: "Medium heading",
	btn_h4: "The smallest heading",

	btn_bold: "Bold",
	btn_italic: "Italic",
	btn_del: "Deleted",

	btn_center: "Center",
	btn_left: "Align to left",
	btn_right: "Align to right",
	btn_justify: "Justify",

	btn_ul: "List",
	btn_ol: "Numbered list",
	btn_olRomans: "Capital roman numbers",
	btn_olRomansSmall: "Small roman numbers",
	btn_olAlphabetSmall: "Small letters",
	btn_olAlphabetBig: "Capital letters",
	
	btn_blockquote: "Quotation block",

	btn_sub: "Lower index",
	btn_sup: "Upper index",

	btn_link: "Link",
	btn_acronym: "Acronym",
	btn_hr: "Line",
	btn_code: "Code",
	btn_codeHtml: "HTML code",
	btn_codeCss: "CSS code",
	btn_codeJs: "Javascript code",
	btn_codePhp: "PHP code",
	btn_codeSql: "SQL code",
	btn_comment: "Comment",
	btn_div: "Div block",
	btn_text: "Text",
	btn_codeInline: "Inline code",
	btn_html: "HTML",
	btn_notexy: "Inline text",

	btn_edit: "Edit",
	btn_preview: "Preview",
	btn_htmlPreview: "HTML",
	btn_syntax: "Texy help",
	btn_submit: "Submit",

	btn_web: "Texyla's web",

	// functions
	texyHeadingText: "Heading text",
	acronymTitle: "Title",
	linkUrl: "Link URL",

	// view
	wait: "Wait please",
	viewEmpty: "Text area is empty!",

	// window
	windowOk: "OK",
	windowClose: "Close",
	windowCancel: "Cancel",
	windowCloseAfterInsert: "Close after insert",

	// url
	syntaxUrl: 'http://texy.info/en/syntax',

	/** plugins ***************************************************************/
	
	// color
	btn_color: "Colors",
	win_color: "Choose a color",
	colorSelectModeHeading: "Colorize:",
	colorSelectModeText: "text",
	colorSelectModeBackground: 'background',
	
	// emoticon
	btn_emoticon: "Emoticon",
	win_emoticon: "Insert an emoticon",

	// files
	btn_files: "Files",
	win_files: "Files",
	filesUpload: "Upload",
	win_upload: "File upload",
	filesFilter: "Filter",
	filesMkDir: "Create directory",
	filesRename: "Rename",
	filesDelete: "Delete",
	filesReallyDelete: "Really delete",
	filesDirectoryName: "Directory name",

	// img
	btn_img: "Image",
	win_img: "Insert an image",
	imgSrc: "Image's address",
	imgAlt: "Description",
	imgAlign: "Alingment",
	imgAlignNone: "none",
	imgAlignLeft: "left",
	imgAlignRight: "right",
	imgAlignCenter: "center",
	imgDescription: "Show as a description",

	// link
	win_link: "Insert hyperlink",
	linkText: "Link text",

	// symbol
	btn_symbol: "Symbol",
	win_symbol: "Insert a symbol",

	// table
	btn_table: "Table",
	win_table: "Insert a table",
	tableCols: "Number of collumns",
	tableRows: "Number of rows",
	tableTh: "Header",
	tableThNone: "none",
	tableThTop: "top",
	tableThLeft: "left",

	// text transform
	btn_textTransform: "Text transformation",
	win_textTransform: "Choose type od transformation",
	textTransformLower: "lower case",
	textTransformUpper: "UPPER CASE",
	textTransformCapitalize: "Capitalize",
	textTransformFirstUpper: "First upper",
	textTransformUrl: "cool-web-url",

	// youtube
	btn_youtube: "YouTube",
	win_youtube: "YouTube",
	youtubeUrl: "Insert address or ID",
	youtubePreview: "Preview",

	// gravatar
	btn_gravatar: "Gravatar",
	win_gravatar: "Gravatar",
	gravatarUrl: "Insert email",
	gravatarPreview: "Preview"
});// ovládání klávesami

// funkce zavádějící ovládání klávesami
jQuery.texyla.initPlugin(function () {
	var _this = this;

	this.textarea.bind(jQuery.browser.opera ? "keypress" : "keydown", function(e) {
		_this.keys(e);
	});
});


jQuery.texyla.extend({
	keys: function(e) {
		var pressedKey = e.charCode || e.keyCode || -1;
	
		var action = false;
	
		// tučně (Ctrl + B nebo např. Shift + Ctrl + B)
		if (e.ctrlKey && pressedKey == 66 && !e.altKey) {			
			this.texy.bold();
			action = true;				
		}
	
		// kurzíva (Ctrl + I nebo např. Alt + Ctrl + I)
		if (e.ctrlKey && pressedKey == 73) {
			this.texy.italic();
			action = true;
		}

		// Zrušit odsazení (shift + tab)
		if (pressedKey == 9 && e.shiftKey) {
			this.texy.unindent();
			action = true;
		}
	
		// tabulátor (tab)
		if (pressedKey == 9 && !e.shiftKey) {
			if (this.texy.update().text().indexOf(this.texy.lf()) == -1) {
				this.texy.tag('\t', '');
			} else {
				this.texy.indent();
			}
			action = true;
		}
	
		// Odeslat formulář (Ctrl + S nebo např. Shift + Ctrl + S)
		if (e.ctrlKey && pressedKey == 83) {
			this.submit();
			action = true;
		}
	
		// zruší defaultní akce
		if (action) {
			// Firefox & Opera (ale ta na to docela sere co se týče klávesových zkratek programu)
			if (e.preventDefault && e.stopPropagation) {
				e.preventDefault();
				e.stopPropagation();
	
			// IE
			} else {
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
		}			
	}
});// Zvětšovací textarea
jQuery.texyla.initPlugin(function () {
	// pokud není načteno jQuery UI resizable, nic se nedělá
	if (typeof(this.textarea.resizable) != "function") return;

	var _this = this;
	this.textarea.resizable({
		handles: 's',
		minHeight: 80,
		stop: function () {
			_this.textareaHeight = _this.textarea.get(0).offsetHeight;
		}
	});

	// fix
	this.textarea.parent().css("padding-bottom", 0);
});// Okno obrázku
jQuery.texyla.addWindow("img", {
	createContent: function () {
		return jQuery(
			'<div><table><tbody><tr>' +
				// Adresa
				'<th><label>' + this.lng.imgSrc + '</label></th>' +
				'<td><input type="text" class="src"></td>' +
			'</tr><tr>' +
				// Alt
				'<th><label>' + this.lng.imgAlt + '</label></th>' +
				'<td><input type="text" class="alt"></td>' +
			'</tr><tr>' +
				// Zobrazit jako popisek
				'<td></td>' +
				'<td><label><input type="checkbox" class="descr">' + this.lng.imgDescription + '</label></td>' +
			'</tr><tr>' +
				// Zarovnání
				'<th><label>' + this.lng.imgAlign + '</label></th>' +
				'<td><select class="align">' +
					'<option value="*">' + this.lng.imgAlignNone + '</option>' +
					'<option value="<">' + this.lng.imgAlignLeft + '</option>' +
					'<option value=">">' + this.lng.imgAlignRight + '</option>' +
					'<option value="<>">' + this.lng.imgAlignCenter + '</option>' +
				'</select></td>' +
			'</tr></tbody></table></div>'
		);
	},

	action: function (el) {
		this.texy.img(
			el.find(".src").val(),
			el.find(".alt").val(),
			el.find(".align").val(),
			el.find(".descr").get(0).checked
		);
	},
	
	dimensions: [350, 250]
});jQuery.texyla.addWindow("table", {
	dimensions: [320, 200],

	action: function (cont) {
		this.texy.table(cont.find(".cols").val(), cont.find(".rows").val(), cont.find(".header").val());
	},

	createContent: function () {
		var _this = this;

		var cont = jQuery(
			"<div style='position:relative'>" +
				'<table class="table"><tbody>' +
				'<tr><th><label>' + this.lng.tableCols + '</label></th><td><input type="number" class="cols" size="3" maxlength="2" min="1" value="2"></td></tr>' +
				'<tr><th><label>' + this.lng.tableRows + '</label></th><td><input type="number" class="rows" size="3" maxlength="2" min="1" value="2"></td></tr>' +
				'<tr><th><label>' + this.lng.tableTh + '</label></th><td><select class="header">' +
				'<option>' + this.lng.tableThNone + '</option>' +
				'<option value="n">' + this.lng.tableThTop + '</option>' +
				'<option value="l">' + this.lng.tableThLeft + '</option>' +
				'</select></td></tr></tbody></table>' +

				// vizuální tabulka - html
				'<div class="tab-background"><div class="tab-selection"></div><div class="tab-control"></div></div>' +
			"</div>"
		);

		// vizuální tabulka
		var resizing = true, posX, posY;

		// povolení nebo zakázání změny velikosti po kliku
		cont.find(".tab-control").click(function (e) {
			resizing = !resizing;

		// změny velikosti apos
		}).mousemove(function (e) {
			if (resizing) {
				posX = e.pageX;
				var el = this;
				while (el.offsetParent) {
					posX -= el.offsetLeft;
					el = el.offsetParent;
				}

				posY = e.pageY;
				el = this;
				while (el.offsetParent) {
					posY -= el.offsetTop;
					el = el.offsetParent;
				}

				var cols = Math.ceil(posX / 8);
				var rows = Math.ceil(posY / 8);

				cont.find(".tab-selection").css({
					width: cols * 8,
					height: rows * 8
				});

				cont.find(".cols").val(cols);
				cont.find(".rows").val(rows);
			}

		// vložení na dvojklik
		}).dblclick(function () {
			_this.getWindowAction("table").call(_this, cont);
			cont.dialog("close");
		});

		cont.find(".cols, .rows").bind("change click blur", function () {
			var cols = Math.min(cont.find(".cols").val(), 10);
			var rows = Math.min(cont.find(".rows").val(), 10);

			cont.find(".tab-selection").css({
				width: cols * 8,
				height: rows * 8
			});
		});

		return cont;
	}
});jQuery.texyla.addWindow("link", {
	dimensions: [330, 180],
	
	createContent: function () {
		return jQuery(
			'<div><table><tbody><tr>' +
				'<th><label>' + this.lng.linkText + '</label></th>' +
				'<td><input type="text" class="link-text" value="' + this.texy.trimSelect().text() + '"></td>' +
			'</tr><tr>' +
				'<th><label>' + this.lng.linkUrl + '</label></th>' +
				'<td><input type="text" class="link-url" value="http://"></td>' +
			'</tr></tbody></table></div>'
		);
	},
	
	action: function (el) {
		var txt = el.find(".link-text").val();
		txt = txt == '' ? '' : '"' + txt + '":';
		this.texy.replace(txt + el.find(".link-url").val());
	}
});// nastavení
$.texyla.setDefaults({
	emoticonPath: "%texyla_base%/emoticons/texy/%var%.gif",
	emoticons: {
		':-)': 'smile',
		':-(': 'sad',
		';-)': 'wink',
		':-D': 'biggrin',
		'8-O': 'eek',
		'8-)': 'cool',
		':-?': 'confused',
		':-x': 'mad',
		':-P': 'razz',
		':-|': 'neutral'		
	}
});

$.texyla.initPlugin(function () {
	this.options.emoticonPath = this.expand(this.options.emoticonPath);
});

$.texyla.addWindow("emoticon", {
	createContent: function () {
		var _this = this;

		var emoticons = $('<div></div>');
		var emoticonsEl = $('<div class="emoticons"></div>').appendTo(emoticons);

		// projít smajly
		for (var i in this.options.emoticons) {
			function emClk(emoticon) {
				return function () {
					_this.texy.replace(emoticon);

					if (emoticons.find("input.close-after-insert").get(0).checked) {
						emoticons.dialog("close");
					}
				}
			};

			$(
				"<img src='" + this.options.emoticonPath.replace("%var%", this.options.emoticons[i]) +
				"' title='" + i + "' alt='" + i + "' class='ui-state-default'>"
			)
				.hover(function () {
					$(this).addClass("ui-state-hover");
				}, function () {
					$(this).removeClass("ui-state-hover");
				})
				.click(emClk(i))
				.appendTo(emoticonsEl);
		}

		emoticons.append("<br><label><input type='checkbox' checked class='close-after-insert'> " + this.lng.windowCloseAfterInsert + "</label>");

		return emoticons;
	},

	dimensions: [192, 170]
});// Výchozí zvláštní znaky
jQuery.texyla.setDefaults({
	symbols: [
		"&", "@", ["<", "&lt;"], [">", "&gt;"], "[", "]", "{", "}", "\\", 
		"α", "β", "π", "µ", "Ω", "∑", "°", "∞", "≠", "±", "×", "÷", "≥",
		"≤", "®", "™", "€", "£", "$", "~", "^", "·", "•"
	]
});

jQuery.texyla.addWindow("symbol", {
	dimensions: [300, 230],

	createContent: function () {
		var _this = this;
		
		var el = jQuery('<div></div>');
		var symbolsEl = jQuery('<div class="symbols"></div>').appendTo(el);

		var symbols = this.options.symbols;

		// projít symboly
		for (var i = 0; i < symbols.length; i++) {
			function clk(text) {
				return function () {
					_this.texy.replace(text);

					if (el.find("input.close-after-insert").get(0).checked) {
						el.dialog("close");
					}
				}
			};

			jQuery("<span class='ui-state-default'></span>")
				.hover(function () {
					jQuery(this).addClass("ui-state-hover");
				}, function () {
					jQuery(this).removeClass("ui-state-hover");
				})
				.text(symbols[i] instanceof Array ? symbols[i][0] : symbols[i])
				.click(clk(symbols[i] instanceof Array ? symbols[i][1] : symbols[i]))
				.appendTo(symbolsEl);
		}

		// kontrolka na zavření po vložení
		el.append(
			"<br><label><input type='checkbox' checked class='close-after-insert'> " +
			this.lng.windowCloseAfterInsert + "</label>"
		);

		return el;
	}
});/**
 * Color plugin
 * Přidává obarvování textu a/nebo pozadí do Texyly.
 * @author Petr Vaněk aka krteczek
 */

jQuery.texyla.setDefaults({
	colors: [
	'red', 'blue', 'aqua', 'black', 'fuchsia', 'gray', 'green', 'lime',
	'maroon', 'navy', 'olive', 'orange', 'purple', 'silver', 'teal',
	'white', 'yellow', '#AABBCC'
	]
});

jQuery.texyla.addWindow("color", {
	createContent: function () {
		var _this = this;
		var colors = jQuery('<div></div>');
		var colorsEl = jQuery('<div class="colors"></div>').appendTo(colors);

		// vloží kód pro obarvení elementu
		function colorClk(color) {
			return function () {
				_this.texy.update();

				// Přidání obarvovacího kódu do textu
				if (_this.texy.isCursor()) {
					_this.texy.selectBlock().phrase('' , ' .{color: ' + color + '}');
				} else {
					_this.texy.phrase('"' , ' .{color: ' + color + '}"');
				}

				// zavření okna po vložení kódu
				if (colors.find("input.close-after-insert").get(0).checked) {
					colors.dialog("close");
				}
			}
		}

		// vytvoření jednotlivých barevných tlačítek
		for (var i = 0; i < _this.options.colors.length; i++) {
			var color = _this.options.colors[i];
			jQuery(
				'<span class="ui-state-default ui-corner-all" title="' + color + '">' +
				'<span style="background-color:' + color + '">&nbsp;</span>' +
				'</span>'
			).hover(function () {
				jQuery(this).addClass("ui-state-hover");
			}, function() {
				jQuery(this).removeClass("ui-state-hover");
			}).click(colorClk(color)).appendTo(colorsEl);
		}

		colors.append(
			"<br><label><input type='checkbox' checked class='close-after-insert'> " +
			this.lng.windowCloseAfterInsert + "</label>"
		);

		return colors;
	},

	dimensions: [200, 150]
});
