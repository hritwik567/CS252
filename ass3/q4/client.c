
/* credit @Daniel Scocco */

/****************** CLIENT CODE ****************/

#include <stdio.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <string.h>
#include <arpa/inet.h>
#define MAXSIZE 1024

int main(){
  int clientSocket, num;
  char buffer[MAXSIZE];
  struct sockaddr_in serverAddr;
  socklen_t addr_size;

  /*---- Create the socket. The three arguments are: ----*/
  /* 1) Internet domain 2) Stream socket 3) Default protocol (TCP in this case) */
  clientSocket = socket(PF_INET, SOCK_STREAM, 0);

  /*---- Configure settings of the server address struct ----*/
  /* Address family = Internet */
  serverAddr.sin_family = AF_INET;
  /* Set port number, using htons function to use proper byte order */
  serverAddr.sin_port = htons(5432);
  /* Set IP address to localhost */
  serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");
  /* Set all bits of the padding field to 0 */
  memset(serverAddr.sin_zero, '\0', sizeof serverAddr.sin_zero);

  /*---- Connect the socket to the server using the address struct ----*/
  addr_size = sizeof serverAddr;
  connect(clientSocket, (struct sockaddr *) &serverAddr, addr_size);

  while(1){
    printf("Enter Data for Server\n");
    fgets(buffer,MAXSIZE-1,stdin);
    if(send(clientSocket, buffer, strlen(buffer), 0)==-1){
      printf("Network Error\n");
      close(clientSocket);
      exit(-1);
    } else {
      /*---- Read the message from the server into the buffer ----*/
      num = recv(clientSocket, buffer, sizeof(buffer), 0);
      if(num<=0){
        printf("Network Error\n");
        break;
      }
      buffer[num] = '\0';
      /*---- Print the received message ----*/
      printf("Data received: %s",buffer);
    }
  }
  return 0;
}
