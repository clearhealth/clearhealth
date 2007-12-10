create table rs_pdaconf
(
ID int(10) unsigned not null default '' auto_increment primary key,
jmeno text default '',
hodnota text default ''
);

# --- Data z tabulky rs_pdaconf ---

insert into rs_pdaconf values('1','PDAkolikClanku','5');
insert into rs_pdaconf values('2','PDAkolikClankuSearch','15');
insert into rs_pdaconf values('3','ScriptShowpage','0');
insert into rs_pdaconf values('4','katShow','1');
insert into rs_pdaconf values('5','HmenuShow','1');
insert into rs_pdaconf values('6','PDAHmenu','<a href=\'index.php\'>Home</a> |
<a href=\'pdatop.php\'>Top 5</a> |
<a href=\'pdasearch.php\'>Hledej</a> |
<a href=\'pdashowpage.php?name=o_pdahps\'>About</a>');
insert into rs_pdaconf values('7','menuShow','0');
insert into rs_pdaconf values('8','PDApatka','(c) HPS team - 
<a href=\'pdashowpage.php?name=o_pdaview\'>PDAview4phpRS</a>');
insert into rs_pdaconf values('9','PDAmenu','<a href=\'pdanej.php\'>Home</a><BR>
<a href=\'pdasearch.php\'>Hledej</a><BR>
<a href=\'pdashowpage.php?name=o_pdahps\'>About</a>');
insert into rs_pdaconf values('10','PDAhlavTitleDef','1');
insert into rs_pdaconf values('11','PDAcharset','windows-1250');
insert into rs_pdaconf values('12','PDAhlavTitleUser','Mùj Titulek');
insert into rs_pdaconf values('13','PDAhlavDate','1');
insert into rs_pdaconf values('14','PDAkolikTop','5');
insert into rs_pdaconf values('15','PDAhlavHeaderDef','1');
insert into rs_pdaconf values('16','PDAhlavHeaderUser','Moje hlavièka');

