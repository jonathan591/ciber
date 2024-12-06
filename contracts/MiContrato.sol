// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract MiContrato {
    struct File {
        string name;
        string description;
        string author;
        string hash;
    }

    // Mapeo de archivos por dirección del propietario
    mapping(address => File) public files;

    function storeFile(string memory _name, string memory _description, string memory _author, string memory _hash) public {
        files[msg.sender] = File(_name, _description, _author, _hash);
    }

    function getFile() public view returns (string memory, string memory, string memory, string memory) {
        File memory file = files[msg.sender];
        return (file.name, file.description, file.author, file.hash);
    }
}

