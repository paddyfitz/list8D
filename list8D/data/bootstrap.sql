SET AUTOCOMMIT=0;

INSERT INTO `role` (`role`) VALUES ('Guest');
INSERT INTO `role` (`role`) VALUES ('Academic');
INSERT INTO `role` (`role`) VALUES ('LibraryStaff');
INSERT INTO `role` (`role`) VALUES ('DepAdmin');

INSERT INTO `user` (`login`, `displayname`, `email`, `institutionid`, `role_id`, `created`, `updated`) VALUES ('lib', 'The Librarian', 'lib@unseen.ac.uk', 'ook', last_insert_id(), 0, 0);

COMMIT;
