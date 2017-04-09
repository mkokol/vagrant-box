#!/usr/bin/perl
# Gavin Brock (http://brock-family.org/gavin/perl) - June 2007
#==============================================================================#

use strict;
use warnings;
use Foundation;

#==============================================================================#

sub kcpassword_xor {
        my ($pass) = @_;

        ### The magic 11 bytes - these are just repeated
        # 0x7D 0x89 0x52 0x23 0xD2 0xBC 0xDD 0xEA 0xA3 0xB9 0x1F
        my @key = qw( 125 137 82 35 210 188 221 234 163 185 31 );

        my $key     = pack "C*", @key;
        my $key_len = length $key;

        for (my $n=0; $n<length($pass); $n+=$key_len) {
                substr($pass,$n,$key_len) ^= $key;
        }

        return $pass;
}

#==============================================================================#

sub write_pass {
        my ($enc) = @_;
        my $file = '/etc/kcpassword';
        open(my $kcp, '>', $file) || die;
        print $kcp $enc;
        close($kcp);
        chmod(0600, $file);
}

#==============================================================================#

sub set_loginwindow {
        my ($user) = @_;
        my $uid  = getpwnam($user) || die;
        my $file = '/Library/Preferences/com.apple.loginwindow.plist';

        ### Use defaults instead of changing plist
        system("defaults write $file \"autoLoginUser\" '$user'");
}

#==============================================================================#

my $user = "$ARGV[0]";
my $pass = "$ARGV[1]";
my $restart = "$ARGV[2]";
my $enc  = kcpassword_xor($pass);

write_pass($enc);
set_loginwindow($user);

### Make this optional
if ($restart) {
        system('killall','loginwindow');
}

exit 0;

#==============================================================================#
