<?php

/**
 * Boost course renderer.
 * Overrides core course renderer.
 *
 * @package   theme_boost
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost\output\core;

defined('MOODLE_INTERNAL') || die();

use cm_info;
use context_course;
use context_module;
use html_writer;
use moodle_url;
use coursecat;
use stdClass;


require_once($CFG->dirroot . "/mod/book/locallib.php");
require_once($CFG->libdir . "/gradelib.php");
require_once($CFG->dirroot . '/course/renderer.php');


class course_renderer extends \core_course_renderer {
    public function book_get_toc($chapters, $chapter, $book, $cm, $edit) {
        global $USER, $OUTPUT;
    
        $toc = '';
        $nch = 0;   // Chapter number
        $ns = 0;    // Subchapter number
        $first = 1;
    
        $context = context_module::instance($cm->id);
        $viewhidden = has_capability('mod/book:viewhiddenchapters', $context);
    
        switch ($book->numbering) {
            case BOOK_NUM_NONE:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc book_toc_none clearfix'));
                break;
            case BOOK_NUM_NUMBERS:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc book_toc_numbered clearfix'));
                break;
            case BOOK_NUM_BULLETS:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc book_toc_bullets clearfix'));
                break;
            case BOOK_NUM_INDENTED:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc book_toc_indented clearfix'));
                break;
        }
    
        if ($edit) { // Editing on (Teacher's TOC).
            $toc .= html_writer::start_tag('ul');
            $i = 0;
            foreach ($chapters as $ch) {
                $i++;
                $title = trim(format_string($ch->title, true, array('context' => $context)));
                $titleunescaped = trim(format_string($ch->title, true, array('context' => $context, 'escape' => false)));
                $titleout = $title;
    
                if (!$ch->subchapter) {
    
                    if ($first) {
                        $toc .= html_writer::start_tag('li');
                    } else {
                        $toc .= html_writer::end_tag('ul');
                        $toc .= html_writer::end_tag('li');
                        $toc .= html_writer::start_tag('li');
                    }
    
                    if (!$ch->hidden) {
                        $nch++;
                        $ns = 0;
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                            $title = "$nch. $title";
                            $titleout = $title;
                        }
                    } else {
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                            $title = "x. $title";
                        }
                        $titleout = html_writer::tag('span', $title, array('class' => 'dimmed_text'));
                    }
                } else {
    
                    if ($first) {
                        $toc .= html_writer::start_tag('li');
                        $toc .= html_writer::start_tag('ul');
                        $toc .= html_writer::start_tag('li');
                    } else {
                        $toc .= html_writer::start_tag('li');
                    }
    
                    if (!$ch->hidden) {
                        $ns++;
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                            $title = "$nch.$ns. $title";
                            $titleout = $title;
                        }
                    } else {
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                            if (empty($chapters[$ch->parent]->hidden)) {
                                $title = "$nch.x. $title";
                            } else {
                                $title = "x.x. $title";
                            }
                        }
                        $titleout = html_writer::tag('span', $title, array('class' => 'dimmed_text'));
                    }
                }
                $toc .= html_writer::start_tag('div', array('class' => 'd-flex'));
                if ($ch->id == $chapter->id) {
                    $toc .= html_writer::tag('strong', $titleout, array('class' => 'text-truncate'));
                } else {
                    $toc .= html_writer::link(new moodle_url('view.php', array('id' => $cm->id, 'chapterid' => $ch->id)), $titleout,
                        array('title' => $titleunescaped, 'class' => 'text-truncate'));
                }
    
                $toc .= html_writer::start_tag('div', array('class' => 'action-list d-flex ml-auto'));
                if ($i != 1) {
                    $toc .= html_writer::link(new moodle_url('move.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'up' => '1', 'sesskey' => $USER->sesskey)),
                            $OUTPUT->pix_icon('t/up', get_string('movechapterup', 'mod_book', $title)),
                            array('title' => get_string('movechapterup', 'mod_book', $titleunescaped)));
                }
                if ($i != count($chapters)) {
                    $toc .= html_writer::link(new moodle_url('move.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'up' => '0', 'sesskey' => $USER->sesskey)),
                            $OUTPUT->pix_icon('t/down', get_string('movechapterdown', 'mod_book', $title)),
                            array('title' => get_string('movechapterdown', 'mod_book', $titleunescaped)));
                }
                $toc .= html_writer::link(new moodle_url('edit.php', array('cmid' => $cm->id, 'id' => $ch->id)),
                        $OUTPUT->pix_icon('t/edit', get_string('editchapter', 'mod_book', $title)),
                        array('title' => get_string('editchapter', 'mod_book', $titleunescaped)));
    
                $deleteaction = new confirm_action(get_string('deletechapter', 'mod_book', $titleunescaped));
                $toc .= $OUTPUT->action_icon(
                        new moodle_url('delete.php', [
                                'id'        => $cm->id,
                                'chapterid' => $ch->id,
                                'sesskey'   => sesskey(),
                                'confirm'   => 1,
                            ]),
                        new pix_icon('t/delete', get_string('deletechapter', 'mod_book', $title)),
                        $deleteaction,
                        ['title' => get_string('deletechapter', 'mod_book', $titleunescaped)]
                    );
    
                if ($ch->hidden) {
                    $toc .= html_writer::link(new moodle_url('show.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'sesskey' => $USER->sesskey)),
                            $OUTPUT->pix_icon('t/show', get_string('showchapter', 'mod_book', $title)),
                            array('title' => get_string('showchapter', 'mod_book', $titleunescaped)));
                } else {
                    $toc .= html_writer::link(new moodle_url('show.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'sesskey' => $USER->sesskey)),
                            $OUTPUT->pix_icon('t/hide', get_string('hidechapter', 'mod_book', $title)),
                            array('title' => get_string('hidechapter', 'mod_book', $titleunescaped)));
                }
    
                $buttontitle = get_string('addafterchapter', 'mod_book', ['title' => $ch->title]);
                $toc .= html_writer::link(new moodle_url('edit.php', array('cmid' => $cm->id, 'pagenum' => $ch->pagenum, 'subchapter' => $ch->subchapter)),
                                                $OUTPUT->pix_icon('add', $buttontitle, 'mod_book'), array('title' => $buttontitle));
                $toc .= html_writer::end_tag('div');
                $toc .= html_writer::end_tag('div');
    
                if (!$ch->subchapter) {
                    $toc .= html_writer::start_tag('ul');
                } else {
                    $toc .= html_writer::end_tag('li');
                }
                $first = 0;
            }
    
            $toc .= html_writer::end_tag('ul');
            $toc .= html_writer::end_tag('li');
            $toc .= html_writer::end_tag('ul');
    
        } else { // Editing off. Normal students, teachers view.
            $toc .= html_writer::start_tag('ul');
            foreach ($chapters as $ch) {
                $toc .= html_writer::tag('strong', "Test", array('class' => "test"));
                $title = trim(format_string($ch->title, true, array('context'=>$context)));
                $titleunescaped = trim(format_string($ch->title, true, array('context' => $context, 'escape' => false)));
                if (!$ch->hidden || ($ch->hidden && $viewhidden)) {
                    if (!$ch->subchapter) {
                        $nch++;
                        $ns = 0;
    
                        if ($first) {
                            $toc .= html_writer::start_tag('li');
                        } else {
                            $toc .= html_writer::end_tag('ul');
                            $toc .= html_writer::end_tag('li');
                            $toc .= html_writer::start_tag('li');
                        }
    
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                              $title = "$nch. $title";
                        }
                    } else {
                        $ns++;
    
                        if ($first) {
                            $toc .= html_writer::start_tag('li');
                            $toc .= html_writer::start_tag('ul');
                            $toc .= html_writer::start_tag('li');
                        } else {
                            $toc .= html_writer::start_tag('li');
                        }
    
                        if ($book->numbering == BOOK_NUM_NUMBERS) {
                              $title = "$nch.$ns. $title";
                        }
                    }
    
                    $cssclass = ($ch->hidden && $viewhidden) ? 'dimmed_text' : '';
    
                    if ($ch->id == $chapter->id) {
                        $toc .= html_writer::tag('strong', $title, array('class' => $cssclass));
                    } else {
                        $toc .= html_writer::link(new moodle_url('view.php',
                                                  array('id' => $cm->id, 'chapterid' => $ch->id)),
                                                  $title, array('title' => s($titleunescaped), 'class' => $cssclass));
                    }
    
                    if (!$ch->subchapter) {
                        $toc .= html_writer::start_tag('ul');
                    } else {
                        $toc .= html_writer::end_tag('li');
                    }
    
                    $first = 0;
                }
            }
    
            $toc .= html_writer::end_tag('ul');
            $toc .= html_writer::end_tag('li');
            $toc .= html_writer::end_tag('ul');
    
        }
    
        $toc .= html_writer::end_tag('div');
    
        $toc = str_replace('<ul></ul>', '', $toc); // Cleanup of invalid structures.
    
        return $toc;
    }
}

?>