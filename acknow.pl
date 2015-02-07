#! /usr/bin/perl

# Generate acknowlegement email.
# Args are tournament code, first name, last name

use DBD::mysql;
use Time::Local;

$tcode = shift @ARGV;
$first = shift @ARGV;
$last = shift @ARGV;

if (length($tcode) == 0  ||  length($first) == 0  ||  length($last) == 0)  {
	print STDERR "Usage: $0 tcode first last\n";
	exit 10;
}

$Database = DBI->connect("DBI:mysql:tournaments", "tourn-reg", "tourn-reg-0601") or die "Cannot open DB";
$qtcode = $Database->quote($tcode);
$qfirst = $Database->quote($first);
$qlast = $Database->quote($last);
$sfh = $Database->prepare("SELECT tname,sdate,ndays,contactfirst,contactlast,email FROM tdetails WHERE tcode=$qtcode");
$sfh->execute;

@row = $sfh->fetchrow_array;
unless  (@row)  {
	print STDERR "Cannot find tournament code $tcode\n";
	exit 20;
}

$tname = shift @row;
$sdate = shift @row;
$ndays = shift @row;
$contactfirst = shift @row;
$contactlast = shift @row;
$contemail = shift @row;

($y,$m,$d) = $sdate =~ /(\d+)-(\d+)-(\d+)/;
$tim = timelocal(0,0,12,$d,$m-1,$y);
@tbits = localtime($tim);
$y = $tbits[5] + 1900;
$d = $tbits[3];
@Mnames = qw/January February March April May June July August September October November December/;
$m = $Mnames[$tbits[4]];
@Days = qw/Sun Mon Tues Wednes Thurs Fri Satur/;
$wd = $Days[$tbits[6]] . "day";

$ddescr = "on $wd $d $m $y";
$ddescr = "starting on $wd $d $m $y" if $ndays > 1;

$sfh = $Database->prepare("SELECT email,lunch FROM ${tcode}_entries WHERE first=$qfirst AND last=$qlast");
$sfh->execute;

@row = $sfh->fetchrow_array;
unless  (@row)  {
	print STDERR "Cannot find player name $first $last\n";
	exit 21;
}

$pemail = shift @row;
$plunch = shift @row;

$tmpfile = "/tmp/splurge$$";
open(MOUT, ">$tmpfile");
print MOUT <<EOF;
To: $first $last<$pemail>
Cc: $contactfirst $contactlast<$contemail>
Reply-to: $contactfirst $contactlast<$contemail>
Subject: Tournament Entry Confirmation

Dear $first $last,

Thank you for your entry for the $tname tournament,
$ddescr, which has been received.

If you have any questions about the tournament, please ask the organiser,
$contactfirst $contactlast, $contemail.

EOF

close MOUT;
$c = system("/usr/sbin/exim -t -oi <$tmpfile");
unlink $tmpfile;
if ($c != 0) {
	print STDERR "Mail sending error\n";
	exit 50;
}
exit 0;
