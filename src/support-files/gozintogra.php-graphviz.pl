#! /usr/bin/perl
###############################################################################
#   (c) 2013 by Christoph Jeschke
###############################################################################
use strict;
use GraphViz;
use XML::Parser;
use XML::Simple;
use XML::SimpleObject;

my($xml, %edges, %nodes);
my $g = GraphViz->new(
            node => {shape => 'record'},
            overlap => 'compress',
            layout => 'neato',
        );

# read xml
###############################################################################
$/ = undef;

while(<>)
{
    $xml = $_;
}

# make tree
###############################################################################
my $x       =   new XML::Parser( Style => 'Tree');
my $xTree   =   $x->parse($xml);
my $oTree   =   XML::SimpleObject->new($xTree);

# create nodes and edges
###############################################################################
foreach my $map ($oTree->children('map'))
{
    foreach my $source ($map->children('source'))
    {
        my $tSource = $source->{_ATTRS}->{file};
        my %edges_count;
        $g->add_node($tSource);

        foreach my $entry ($source->children('entry'))
        {

            $g->add_node($entry->{_ATTRS}->{target});

            foreach my $target ($entry->{_ATTRS})
            {
                $edges_count{$target->{target}}++;
            }
        }

        foreach my $tTarget (keys(%edges_count))
        {
            $g->add_edge($tSource, $tTarget, label => $edges_count{$tTarget});
        }
    }
}

print $g->as_png;
